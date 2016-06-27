<?

class NotifyClient {
    public $host;
    public $port;
    public $socket;

    public function __construct($host,$port){
        $this->host = $host;
        $this->port = $port;    
    }
    public function init(){
        $this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 1, 'usec' => 0));
        $b = socket_connect($this->socket, $this->host, $this->port);
        if($b == false){
            //echo 'connect to notify server failed\n';
            return false; 
        }else{
            //echo "connected\n";
        }
        return true;
    }
    public function push($event_type, $ts, $count, $id) {
        $socket = $this->socket;
        
        $buf = $event_type.",".$ts.",".$count.",".$id;
        //echo "push ".$buf."\n";
        $header = strlen($buf);
        $header = $header | 0x01000000;
        $header_str = pack('i',$header);
        if(socket_send($socket,$header_str.$buf,4+strlen($buf),0) == false){
            //echo "send push query failed\n";
            return false;
        }
        $ret = socket_recv($socket,$buf,4,0);
        if($ret != 4){
            return false;
        }
        $code = unpack('i',$buf);
        if($code[1]==0){
            return true;
        }else{
            return false;
        }
    }
    public function fetch() {
        $socket = $this->socket;
        
        $header = 0;
        $header = $header | 0x02000000;
        $header_str = pack('i',$header);
        if(socket_send($socket,$header_str,4,0) == false){
            //echo "send push query failed\n";
            return false;
        }
        $ret = socket_recv($socket,$buf,4,0);
        if($ret != 4){
            //echo "recv header failed\n";
            return false;
        }
        $code = unpack('i',$buf);
        if($code[1] == 0){
            $ret = socket_recv($socket,$buf,4,0);     
            if($ret != 4){
                //echo "recv len failed\n";
                return false;
            }
            $len = unpack('i',$buf);
            $ret = socket_recv($socket,$buf,$len[1],0);     
            if($ret != $len[1]){
                //echo "recv data failed\n";
                return false;
            }
            $list = explode(',',$buf);
            //echo "fetched ".$buf."\n";
            return $list;
        }else{
            //echo "notify_manager internal error".$code[1]."\n";
        }
        return false;
    }    
}


?>
