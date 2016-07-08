
#_*_encoding:utf-8_*_
#encoding = utf-8
import MySQLdb
from flask import Flask,g,request,make_response,render_template,redirect
import hashlib
from WXBizMsgCrypt import WXBizMsgCrypt
from WXBizMsgCrypt_Service import WXBizMsgCrypt_Service
import urllib
from urllib import unquote
import time
import xml.etree.ElementTree as ET
import urllib2
#import models
import json
import qrcode
from StringIO import StringIO
from datetime import datetime,timedelta, date
#from sae.storage import Bucket

app = Flask(__name__)
app.debug = True

def parse_msg():
    recvmsg = request.stream.read()
    root = ET.fromstring(recvmsg)
    msg = {}
    for child in root:
        msg[child.tag] = child.text
    return msg

@app.before_request
def before_request():
    g.db = MySQLdb.connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS,
                           MYSQL_DB, port=int(MYSQL_PORT))

@app.teardown_request
def teardown_request(exception):
    if hasattr(g, 'db'): g.db.close()

@app.route('/')
def home():
    return render_template('index.html')

@app.route('/menu')
def menu():
    return render_template('template.html')

@app.route('/demo', methods=['GET', 'POST'])
def greeting():
    html = ''

    if request.method == 'POST':
        c = g.db.cursor()
        c.execute("insert into demo(text) values(%s)", (request.form['text']))

    html += """
    <form action="" method="post">
        <div><textarea cols="40" name="text"></textarea></div>
        <div><input type="submit" /></div>
    </form>
    """
    c = g.db.cursor()
    c.execute('select * from demo')
    msgs = list(c.fetchall())
    msgs.reverse()
    for row in msgs:
        html +=  '<p>' + row[-1] + '</p>'

    return html

@app.route('/weixin', methods=['GET'])
def weixin_verify():
    sToken = "iU6vCIAFJbMJP"
    sEncodingAESKey = "7BDCRgCfG9OJpQkThW8KKXzFdlJvMrt1KM2wjJV7Sde"
    sCorpID = "wx9d8437176bfb548a"

    wxcpt = WXBizMsgCrypt(sToken,sEncodingAESKey,sCorpID)

    sVerifyMsgSig = request.args.get("msg_signature")
    sVerifyTimeStamp = request.args.get("timestamp")
    sVerifyNonce = request.args.get("nonce")
    sVerifyEchoStr = unquote(request.args.get("echostr"))
    
    ret,sEchoStr = wxcpt.VerifyURL(sVerifyMsgSig, sVerifyTimeStamp, sVerifyNonce, sVerifyEchoStr)

    if(ret!=0):
      print "ERR: VerifyURL ret: " + str(ret)
      sys.exit(1)
    
    return sEchoStr

def get_access_token():
    accessUrl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxa6c94baff0f5faff&secret=faee250fe458c4ddd3579a152b523d06"
    f = urllib2.urlopen(accessUrl)
    accessT = f.read().decode("utf-8")
    jsonT = json.loads(accessT)
    return jsonT["access_token"]

def get_user_info(access_token, open_id):
    accessUrl = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN" % (access_token, open_id)
    f = urllib2.urlopen(accessUrl)
    accessT = f.read().decode("utf-8")
    jsonT = json.loads(accessT)
    print jsonT
    return jsonT
"""
def create_user_accout(user_name, community=""):
    # user_name here is the open_id return by wechat
    try:
        user_name = unicode(user_name).encode("utf-8")
        print user_name

        q = models.session.query(models.User.id).filter_by(open_id = user_name).first()
        if q is None:
            print "None"
        else:
            print "Exist"
            return

        models.session.flush()

        access_token = get_access_token()
        user_info = get_user_info(access_token, user_name)
        print "user_info: " + str(user_info)

        TIMEFORMAT = '%Y-%m-%d'
        join_time = time.strftime(TIMEFORMAT, time.localtime(time.time()))

        u = models.User()
        u.open_id = user_name
        u.alias = user_info['nickname'].encode("utf-8")
        u.community = community
        u.join_time = join_time
        print u.alias
        
        models.session.add(u)
        models.session.flush()
        models.session.commit()
    except Exception,ex:
        print Exception,":",ex

def create_user_accout_from_json(user_info):
    try:
        if 'follow_time' not in user_info or user_info['follow_time'] is None:
            TIMEFORMAT = '%Y-%m-%d'
            join_time = time.strftime(TIMEFORMAT, time.localtime(time.time()))
        else:
            join_time = user_info['follow_time'][:10]

        u = models.User()
        u.open_id = user_info['weixin_openid']
        u.alias = user_info['nick'].encode("utf-8")
        u.join_time = join_time
        u.sex = user_info['sex']
        u.traded_num = user_info['traded_num']
        u.traded_money = user_info['traded_money']
        u.points = user_info['points']
        u.avatar = user_info['avatar']
        print u.alias
        
        models.session.add(u)
        models.session.flush()
        models.session.commit()
    except Exception,ex:
        print Exception,":",ex
""" 
@app.route("/weixin", methods=['POST'])
def response_msg():
    print "==========Request received=========="
    msg = parse_msg()
    print msg

    sToken = "iU6vCIAFJbMJP"
    sEncodingAESKey = "7BDCRgCfG9OJpQkThW8KKXzFdlJvMrt1KM2wjJV7Sde"
    sCorpID = "wx9d8437176bfb548a"

    wxcpt = WXBizMsgCrypt(sToken,sEncodingAESKey,sCorpID)
    ret,xml_content = wxcpt.DecryptMsg(msg['Encrypt'])

    print "xml_content: " + xml_content

    xml_rep = ""
    rep_txt = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[%s]]></Content></xml>"
    rep_imgtxt = """<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[news]]></MsgType>
                <ArticleCount>3</ArticleCount>
                <Articles>
                <item>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[xx]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>
                <item>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[xx]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>
                <item>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[xx]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>
                </Articles>
                </xml>"""

    rep_menutxt = """<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[news]]></MsgType>
                <ArticleCount>5</ArticleCount>
                <Articles>
                <item>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[xx]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>
                <item>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[xx]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>
                <item>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[xx]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>
                <item>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[xx]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>
                <item>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[xx]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>
                </Articles>
                </xml>"""

    xml_tree = ET.fromstring(xml_content)
    event_key = xml_tree.find("EventKey").text
    fromuser_name = xml_tree.find("FromUserName").text
    touser_name = xml_tree.find("ToUserName").text

    # register user to chef if there is no accout related to current user name
    #create_user_accout(fromuser_name)

    if event_key == 'Special':
        xml_rep = rep_imgtxt % (fromuser_name, touser_name, str(int(time.time())), unicode("招牌新品").encode("utf-8"), 'http://5starselfchef-menus.stor.sinaapp.com/a.jpg', 'http://5starselfchef-menus.stor.sinaapp.com/template.html', unicode("今日推荐").encode("utf-8"), 'http://5starselfchef-menus.stor.sinaapp.com/d.jpg', 'http://www.microsoft.com', unicode("主题活动").encode("utf-8"), 'http://5starselfchef-menus.stor.sinaapp.com/h.jpg', 'http://www.microsoft.com')
    elif event_key == 'Menu':
        xml_rep = rep_menutxt % (fromuser_name, touser_name, str(int(time.time())), unicode("白灼基围虾").encode("utf-8"), 'http://5starselfchef-menus.stor.sinaapp.com/e.jpg', 'http://5starselfchef.sinaapp.com/oauth', unicode("煎酿三宝").encode("utf-8"), 'http://5starselfchef-menus.stor.sinaapp.com/f.jpg', 'http://www.microsoft.com', unicode("宫保鸡丁").encode("utf-8"), 'http://5starselfchef-menus.stor.sinaapp.com/p800_201109280814160.jpg', 'https://qy.weixin.qq.com/cgi-bin/show?agentuin=1098008973&fileid=10000003&offset=0', unicode("铁板鱿鱼丝").encode("utf-8"), 'http://5starselfchef-menus.stor.sinaapp.com/g.jpg', 'http://www.microsoft.com', unicode("鲜虾寿司").encode("utf-8"), 'http://5starselfchef-menus.stor.sinaapp.com/a.jpg', 'http://www.microsoft.com')
    elif event_key == 'Order':
        xml_rep = rep_txt % (fromuser_name, touser_name, str(int(time.time())), unicode(fromuser_name + '您好，下单成功，谢谢！').encode("utf-8"))
    print xml_rep

    sVerifyNonce = request.args.get("nonce")
    ret, response = wxcpt.EncryptMsg(xml_rep, sVerifyNonce)
    print "response xml: " + response
    return response
    """
    response = make_response(xml_rep % ('zilchist', msg['ToUserName'], str(int(time.time())), u"是日菜单", u"主厨介绍", 'http://5starselfchef-menus.stor.sinaapp.com/chef.jpg', u"照烧鸡"))
    response.content_type = 'application/xml'

    print response
    return response
    """
@app.route('/auth_callback')
def auth_callback():
    print "====inside callback===="
    msg = urllib2.urlopen('https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=wx9d8437176bfb548a&corpsecret=BgOt8JF3QrWnA4AVia4MczaUIW3gIrZQPTXGMG4UEdfLeprDAlnv5EbDybKG8cFc').read()
    print "token message: " + msg
    code = request.args.get('code')
    print "code: " + code
    token = json.loads(msg)
    response = urllib2.urlopen('https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=' + token['access_token'] + '&code=' + code).read()
    #response = '<html><head></head><body><h1>%s</h1></body></html>' % code
    return response

### For Service Account ###

@app.route('/service', methods=['GET'])
def service_verify():
    sToken = "iU6vCIAFJbMJP"
    sEncodingAESKey = "7BDCRgCfG9OJpQkThW8KKXzFdlJvMrt1KM2wjJV7Sde"
    sAppID = "wxa6c94baff0f5faff"

    wxcpt = WXBizMsgCrypt_Service(sToken, sEncodingAESKey, sAppID)

    sVerifyMsgSig = request.args.get("signature")
    sVerifyTimeStamp = request.args.get("timestamp")
    sVerifyNonce = request.args.get("nonce")
    sVerifyEchoStr = unquote(request.args.get("echostr"))
    
    ret = wxcpt.VerifyURL(sVerifyMsgSig, sVerifyTimeStamp, sVerifyNonce)

    if(ret!=0):
      print "ERR: VerifyURL ret: " + str(ret)
      sys.exit(1)
    
    return sVerifyEchoStr

@app.route("/service", methods=['POST'])
def response_service():
    print "==========Request received=========="
    msg = parse_msg()
    print msg

    sToken = "iU6vCIAFJbMJP"
    sEncodingAESKey = "7BDCRgCfG9OJpQkThW8KKXzFdlJvMrt1KM2wjJV7Sde"
    sAppID = "wxa6c94baff0f5faff"

    wxcpt = WXBizMsgCrypt_Service(sToken, sEncodingAESKey, sAppID)

    sVerifyMsgSig = request.args.get("signature")
    sVerifyTimeStamp = request.args.get("timestamp")
    sVerifyNonce = request.args.get("nonce")
    print "Debug=====: " + sVerifyMsgSig + "," + sVerifyTimeStamp + "," + sVerifyNonce
    ret,xml_content = wxcpt.DecryptData(msg['Encrypt'], sVerifyMsgSig, sVerifyTimeStamp, sVerifyNonce)

    print "xml_content: " + xml_content

    xml_rep = ""
    rep_txt = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[%s]]></Content></xml>"
    rep_imgtxt = """<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[news]]></MsgType>
                <ArticleCount>3</ArticleCount>
                <Articles>
                <item>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[xx]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>
                <item>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[xx]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>
                <item>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[xx]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>
                </Articles>
                </xml>"""

    rep_menutxt = """<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[news]]></MsgType>
                <ArticleCount>5</ArticleCount>
                <Articles>
                <item>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[xx]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>
                <item>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[xx]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>
                <item>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[xx]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>
                <item>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[xx]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>
                <item>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[xx]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>
                </Articles>
                </xml>"""

    xml_tree = ET.fromstring(xml_content)
    event_key = xml_tree.find("EventKey").text
    fromuser_name = xml_tree.find("FromUserName").text
    touser_name = xml_tree.find("ToUserName").text

    # register user to chef if there is no accout related to current user name
    #create_user_accout(fromuser_name)

    if event_key == 'Special':
        xml_rep = rep_imgtxt % (fromuser_name, touser_name, str(int(time.time())), unicode("招牌新品").encode("utf-8"), 'http://5starselfchef-menus.stor.sinaapp.com/a.jpg', 'http://5starselfchef-menus.stor.sinaapp.com/template.html', unicode("今日推荐").encode("utf-8"), 'http://5starselfchef-menus.stor.sinaapp.com/d.jpg', 'http://wap.koudaitong.com/v2/home/o0hoytzw', unicode("主题活动").encode("utf-8"), 'http://5starselfchef-menus.stor.sinaapp.com/h.jpg', 'http://www.microsoft.com')
    elif event_key == 'Menu':
        xml_rep = rep_menutxt % (fromuser_name, touser_name, str(int(time.time())), unicode("白灼基围虾").encode("utf-8"), 'http://5starselfchef-menus.stor.sinaapp.com/e.jpg', 'http://5starselfchef.sinaapp.com/oauth', unicode("煎酿三宝").encode("utf-8"), 'http://5starselfchef-menus.stor.sinaapp.com/f.jpg', 'http://www.microsoft.com', unicode("宫保鸡丁").encode("utf-8"), 'http://5starselfchef-menus.stor.sinaapp.com/p800_201109280814160.jpg', 'https://qy.weixin.qq.com/cgi-bin/show?agentuin=1098008973&fileid=10000003&offset=0', unicode("铁板鱿鱼丝").encode("utf-8"), 'http://5starselfchef-menus.stor.sinaapp.com/g.jpg', 'http://www.microsoft.com', unicode("鲜虾寿司").encode("utf-8"), 'http://5starselfchef-menus.stor.sinaapp.com/a.jpg', 'http://www.microsoft.com')
    elif event_key == 'Order':
        xml_rep = rep_txt % (fromuser_name, touser_name, str(int(time.time())), unicode(fromuser_name + '您好，下单成功，谢谢！').encode("utf-8"))
    print xml_rep

    sVerifyNonce = request.args.get("nonce")
    ret, response = wxcpt.EncryptMsg(xml_rep, sVerifyNonce)
    print "response xml: " + response
    return response
    """
    response = make_response(xml_rep % ('zilchist', msg['ToUserName'], str(int(time.time())), u"是日菜单", u"主厨介绍", 'http://5starselfchef-menus.stor.sinaapp.com/chef.jpg', u"照烧鸡"))
    response.content_type = 'application/xml'

    print response
    return response
    """

def construct_url(params):
    secret = '82778e43a9dbb94b0b75616526e22747'

    vals = []
    for param in params:
        vals.append(param + str(params[param]))
    vals.sort()
    sig = "".join(vals)
    sig = secret + sig + secret
    #print sig

    md5 = hashlib.md5()
    md5.update(sig)
    dig = md5.hexdigest()

    vals = []
    for param in params:
        vals.append(param + '=' + str(params[param]))
    tail = "&".join(vals)

    tail = tail.replace(' ', '%20') #urllib.quote(tail)
    url = 'https://open.koudaitong.com/api/entry?sign=' + dig + '&' + tail
    print url

    return url

@app.route('/qrcode', methods=['GET'])
def get_qr_code():
    print "==========QRCode Request received=========="

    open_id = request.args.get("openid")
    print open_id

    qr = qrcode.QRCode()
    qr.add_data("http://5starselfchef.sinaapp.com/trades_info?openid=" + open_id)
    qr.make(fit=True)
    img = qr.make_image()

    output = StringIO()
    img.save(output)
    img_data = output.getvalue()
    output.close()

    response = make_response(img_data)
    response.headers['Content-Type'] = 'image/jpeg'
    response.headers['Content-Disposition'] = 'attachment; filename=qr_code.jpg'
    return response

@app.route('/trades_info', methods=['GET'])
def get_trades_info():
    print "==========Trades info Request received=========="

    open_id = request.args.get("openid")
    current_time = datetime.now()

    params = {}

    # application params
    params['weixin_openid'] = open_id

    # communication params
    params['app_id'] = '45180df80b914de7f8'
    params['method'] = 'kdt.users.weixin.follower.get'
    params['timestamp'] = current_time.strftime("%Y-%m-%d %H:%M:%S")
    params['format'] = 'json'
    params['v'] = '1.0'
    params['sign_method'] = 'md5'

    url = construct_url(params)
    msg = urllib2.urlopen(url).read()
    print msg

    msg_json = json.loads(msg)

    user = {}
    user['nick'] = msg_json['response']['user']['nick']
    user['id'] = msg_json['response']['user']['user_id']
    user['logo'] = msg_json['response']['user']['avatar']

    ########### get trades ###########

    params = {}

    # application params
    #params['status'] = 'TRADE_BUYER_SIGNED'
    params['status'] = 'WAIT_SELLER_SEND_GOODS'
    #params['weixin_user_id'] = 1
    #params['buyer_nick'] = user['nick'].encode('utf-8')

    one_day_ago = current_time + timedelta(days=-3)
    params['start_created'] = one_day_ago.strftime("%Y-%m-%d %H:%M:%S")

    # communication params
    params['app_id'] = '45180df80b914de7f8'
    params['method'] = 'kdt.trades.sold.get'
    params['timestamp'] = current_time.strftime("%Y-%m-%d %H:%M:%S")
    params['format'] = 'json'
    params['v'] = '1.0'
    params['sign_method'] = 'md5'

    url = construct_url(params)
    msg = urllib2.urlopen(url).read()
    print msg

    msg_json = json.loads(msg)

    items = []
    if int(msg_json["response"]["total_results"]) > 0:
        trades = msg_json["response"]["trades"]
        for trade in trades:
            buyer_nick = trade["buyer_nick"]
            if buyer_nick == user['nick']:
                user['mobile'] = trade["receiver_mobile"]
                user['pay_time'] = trade["pay_time"]
                user['message'] = trade["buyer_message"]
                user['tid'] = trade["tid"]
                print "Got trade, user info: "
                print user

                orders = trade["orders"]
                for order in orders:
                    item = {}
                    item['name'] = order["title"]
                    item['num'] = order["num"]
                    items.append(item)
                    print item
    
    return render_template('order.html', user=user, items=items)

@app.route('/ship', methods=['GET'])
def ship_confirm():
    print "==========Ship confirm request received=========="

    params = {}
    params['tid'] = request.args.get("tid")
    params['is_no_express'] = 1

    # communication params
    current_time = datetime.now()
    params['app_id'] = '45180df80b914de7f8'
    params['method'] = 'kdt.logistics.online.confirm'
    params['timestamp'] = current_time.strftime("%Y-%m-%d %H:%M:%S")
    params['format'] = 'json'
    params['v'] = '1.0'
    params['sign_method'] = 'md5'

    url = construct_url(params)
    msg = urllib2.urlopen(url).read()
    print msg

    return msg

@app.route('/register', methods=['GET', 'POST'])
def reg_new_user():
    print "==========Register Request received=========="

    print request.args

    msg = parse_msg()
    print msg

    #token signature verification
    sToken = "iU6vCIAFJbMJP"
    sEncodingAESKey = "7BDCRgCfG9OJpQkThW8KKXzFdlJvMrt1KM2wjJV7Sde"
    sAppID = "wxa6c94baff0f5faff"

    wxcpt = WXBizMsgCrypt_Service(sToken, sEncodingAESKey, sAppID)

    sVerifyMsgSig = request.args.get("signature")
    sVerifyTimeStamp = request.args.get("timestamp")
    sVerifyNonce = request.args.get("nonce")
    
    ret = wxcpt.VerifyURL(sVerifyMsgSig, sVerifyTimeStamp, sVerifyNonce)

    if(ret!=0):
        print "ERR: VerifyURL ret: " + str(ret)
        return ""

    if msg['Event'] == 'unsubscribe':
        print "unsubscribe, do nothing"
        return ""

    open_id = msg['FromUserName']
    community = request.args.get("commu")
    #create_user_accout(open_id, community)

    return ""

@app.route('/comm_select', methods=['GET'])
def select_community():
    return redirect("https://wap.koudaitong.com/v2/showcase/feature?alias=1c2tcjjra", code=302)

############### For Data Transfering ################

def get_fans_helper():
    print "==========Fans info helper Request received=========="

    current_time = datetime.now()

    params = {}
    result = {}
    result['total_results'] = 0
    result['users'] = []

    start_date = date(2015, 10, 18)
    end_date = date(2016, 7, 7)
    for single_date in daterange(start_date, end_date):
        end = single_date.strftime("%Y-%m-%d 00:00:00")
        start = (single_date - timedelta(1)).strftime("%Y-%m-%d 00:00:00")

        # application params
        params['start_follow'] = start
        params['end_follow'] = end
        params['page_size'] = 500

        # communication params
        params['app_id'] = '45180df80b914de7f8'
        params['method'] = 'kdt.users.weixin.followers.get'
        params['timestamp'] = current_time.strftime("%Y-%m-%d %H:%M:%S")
        params['format'] = 'json'
        params['v'] = '1.0'
        params['sign_method'] = 'md5'

        url = construct_url(params)
        msg = urllib2.urlopen(url).read()
        print msg

        msg_json = json.loads(msg)

        if 'total_results' not in msg_json['response']:
            continue
        result['total_results'] += msg_json['response']['total_results']
        if 0 == msg_json['response']['total_results']:
            continue
        result['users'].extend(msg_json['response']['users'])

    print "Get total fans: " + str(result['total_results'])
    fo = open('user.txt', 'w')
    fo.write(json.dumps(result))
    fo.close()
    return result

def daterange(start_date, end_date):
    for n in range(int ((end_date - start_date).days)):
        yield start_date + timedelta(n)

@app.route('/fans_info', methods=['GET'])
def get_fans_info():
    print "==========Fans info Request received=========="

    result = get_fans_helper()

    return json.dumps(result)

@app.route('/dump_fans_info', methods=['GET'])
def dump_fans_info():
    print "==========Fans info Request received=========="

    fans = get_fans_helper()

    #for fan in fans['users']:
        #create_user_accout_from_json(fan)

    return "Dumped total fans: " + str(fans['total_results'])

#@app.route('/trades_info_all', methods=['GET'])
def get_all_trades_info():
    print "==========All Trades info Request received=========="

    current_time = datetime.now()

    params = {}

    page_no = 1
    # application params
    params['page_no'] = page_no
    params['page_size'] = 100
    params['use_has_next'] = True

    # communication params
    params['app_id'] = '45180df80b914de7f8'
    params['method'] = 'kdt.trades.sold.get'
    params['timestamp'] = current_time.strftime("%Y-%m-%d %H:%M:%S")
    params['format'] = 'json'
    params['v'] = '1.0'
    params['sign_method'] = 'md5'

    has_next = True
    result = {}
    while has_next:
        params['page_no'] = page_no
        url = construct_url(params)
        msg = urllib2.urlopen(url).read()

        #print msg
        obj = json.loads(msg)
        if 1 == page_no:
            result = obj
        else:
            result['response']['trades'].extend(obj['response']['trades'])

        page_no += 1
        has_next = bool(obj['response']['has_next'])

    #bucket = Bucket('menus')
    #bucket.put_object('order_3.txt', json.dumps(result))
    fo = open('order.txt', 'w')
    fo.write(json.dumps(result))
    fo.close()
    
    return msg

def user_update():
    """
    new = session.query(News).filter(News.tid==0).first()
    new.tid = 100
    session.commit()
    """
    
if __name__ == "__main__":
    #get_all_trades_info()
    get_fans_helper()

