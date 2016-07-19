
#_*_encoding:utf-8_*_
#encoding = utf-8
import MySQLdb
from flask import Flask,g,request,make_response,render_template,redirect
import hashlib
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
import MySQLdb
import json
import sys
#from sae.storage import Bucket
import traceback

app = Flask(__name__)
app.debug = True

def parse_msg():
    recvmsg = request.stream.read()
    root = ET.fromstring(recvmsg)
    msg = {}
    for child in root:
        msg[child.tag] = child.text
    return msg

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

############### For Data Transfering ################

def get_fans_helper():
    print "==========Fans info helper Request received=========="

    current_time = datetime.now()

    params = {}
    result = {}
    
    result['total_results'] = 0
    result['users'] = []

    start_time = "2015-10-10 10:10:10"
    start_date = datetime.strptime(start_time, "%Y-%m-%d %H:%M:%S")
    end_date = current_time
    for single_date in daterange(start_date, end_date):
        end = single_date
        start = single_date - timedelta(1)

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
        #print msg

        msg_json = json.loads(msg)

        if 'total_results' not in msg_json['response']:
            continue
        result['total_results'] += msg_json['response']['total_results']
        if 0 == msg_json['response']['total_results']:
            continue
        result['users'].extend(msg_json['response']['users'])

    print "Get total fans: " + str(result['total_results'])
    fo = open('/home/ubuntu/chef/data/users_' + current_time.strftime("%Y%m%d") + '.json', 'w')
    fo.write(json.dumps(result))
    fo.close()
    """
    fi = open('users_' + current_time.strftime("%Y%m%d") + '.json', 'r')
    result = json.loads(fi.readline())
    """

    users_injection(result)

    return result

def daterange(start_date, end_date):
    for n in range(int ((end_date - start_date).days)):
        yield end_date - timedelta(n)

def users_injection(users):
    add_users = 0
    update_users = 0
    try:
        for user_info in users['users']:
            if 'follow_time' not in user_info or user_info['follow_time'] is None:
                TIMEFORMAT = '%Y-%m-%d'
                join_time = time.strftime(TIMEFORMAT, time.localtime(time.time()))
            else:
                join_time = user_info['follow_time'][:10]

            open_id = user_info['weixin_openid']
            youzan_id = user_info['user_id']
            alias = user_info['nick']#.encode("utf-8")
            sex = user_info['sex']
            traded_num = user_info['traded_num']
            traded_money = user_info['traded_money']
            points = user_info['points']
            avatar = user_info['avatar']

            sql = "SELECT COUNT(*) FROM `users` WHERE `open_id`='%s'" % open_id
            cur.execute(sql)
            nor = cur.fetchone()

            if 0 < nor[0]: #user information exists
                sql = "UPDATE users SET traded_num = " + str(traded_num)
                sql += ", traded_money = " + str(traded_money)
                sql += ", points = " + str(points)
                sql += " WHERE open_id = '" + open_id + "'"
                update_users += 1
            else:
                sql = "INSERT INTO `users` (`open_id`, `youzan_id`, `alias`, `join_time`, `sex`, `traded_num`, `traded_money`, `points`, `avatar`) VALUES ('"
                sql += open_id + "', '"
                sql += youzan_id + "', '"
                sql += alias + "', '"
                sql += join_time + "', '"
                sql += sex + "', "
                sql += str(traded_num) + ", "
                sql += str(traded_money) + ", "
                sql += str(points) + ", '"
                sql += avatar + "');"
                add_users += 1
                #print sql

            #print sql
            cur.execute(sql)
    except Exception,ex:
        print Exception,":",ex
        traceback.print_exc()

    print "add %s new users" % add_users
    print "update %s new users" % update_users

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
    order_file = '/home/ubuntu/chef/data/order_' + current_time.strftime("%Y%m%d") + '.json'
    print "start to write to file %s of %d records" % (order_file, len(result['response']['trades']))
    fo = open(order_file, 'w')
    fo.write(json.dumps(result))
    fo.close()

    trades_injection(result)
    process_deliver_time(result)
    
    return msg

def trades_injection(trades):
    print len(trades['response']['trades'])

    current_time = datetime.now()
    baseline_time = current_time - timedelta(7)
    baseline_time = baseline_time.strftime("%Y-%m-%d %H:%M:%S") 

    add_trades = 0
    modify_trades = 0
    try:
        for trade in trades['response']['trades']:
            consign_time = trade['pay_time']
            if '' == consign_time:
                consign_time = "2015-10-10 10:10:10"
            youzan_id = trade['weixin_user_id']
            order_num = trade['num']
            address = trade['receiver_address']
            pay_type = trade['pay_type']
            district = trade['receiver_district']
            message = trade['buyer_message']
            post_fee = trade['post_fee']
            tid = trade['tid']
            status = trade['status']
            mobile = trade['receiver_mobile']
            payment = trade['payment']
            discount_fee = trade['discount_fee']
            adjust_fee = trade['adjust_fee']
            total_fee = trade['total_fee']
            receiver_name = trade['receiver_name']
            
            sql = "UPDATE users SET mobile = '" + mobile + "'"
            if len(receiver_name) > 0:
                sql += ", name = '" + receiver_name + "'"
            if len(address) > 0:
                sql += ", address = '" + address + "'"
            
            sql +=  " WHERE youzan_id = '" + youzan_id + "'"
            #print sql
            cur.execute(sql)

            sql = "SELECT COUNT(*) FROM `trades` WHERE `tid`='%s'" % tid
            cur.execute(sql)
            nor = cur.fetchone()

            if 0 >= nor[0]: #trade information not exists
                add_trades += 1
                
                sql = "INSERT INTO `trades` (`tid`, `youzan_id`, `order_num`, `pay_type`, `district`, `post_fee`, `payment`, `discount`, `total_fee`, `message`, `status`, `consign_time`) VALUES ('"
                sql +=  tid + "', '"
                sql += youzan_id + "', "
                sql += str(order_num) + ", '"
                sql += pay_type + "', '"
                sql += district + "', "
                sql += str(post_fee) + ", "
                sql += str(payment) + ", "
                sql += str(discount_fee) + ", "
                #sql += str(adjust_fee) + ", "
                sql += str(total_fee) + ", '"
                sql += message + "', '"
                sql += status + "', '"
                sql += consign_time + "');"
                #print sql

                cur.execute(sql)
                
                
                for order in trade['orders']:
                    name = order['title']
                    cut = len(name)
                    splash = name.find('/')
                    if splash >= 0 and splash < cut:
                        cut = splash
                    cnbrack = name.find("（".decode('utf-8'))
                    if cnbrack >= 0 and cnbrack < cut:
                        cut = cnbrack
                    enbrack = name.find('(')
                    if enbrack >= 0 and enbrack < cut:
                        cut = enbrack
                    name = name[:cut]

                    msgs = order['buyer_messages']
                    message = "|"
                    for msg in msgs:
                        if "" != msg['content']:
                            message += msg['content'] + '|'

                    sql = "INSERT INTO `orders` (`oid`, `tid`, `youzan_id`, `name`, `message`, `price`, `num`, `payment`, `discount`, `total`, `state`) VALUES ('"
                    sql +=  str(order['oid']) + "', '"
                    sql += tid + "', '"
                    sql += youzan_id + "', '"
                    sql += name + "', '"
                    sql += message + "', "
                    sql += str(order['price']) + ", "
                    sql += str(order['num']) + ", "
                    sql += str(order['payment']) + ", "
                    sql += str(order['discount_fee']) + ", "
                    sql += str(order['total_fee']) + ", '"
                    sql += order['state_str'] + "');"

                    #print sql
                    cur.execute(sql)
            else:
                if consign_time > baseline_time:
                    modify_trades += 1
                    sql = "UPDATE trades SET pay_type = '" + str(pay_type)
                    sql += "', payment = " + str(payment)
                    sql += ", status = '" + str(status)
                    sql += "', consign_time = '" + str(consign_time)
                    sql += "' WHERE tid = '" + tid + "'"
                    #print sql
                    cur.execute(sql)
    except Exception,ex:
        print Exception,":",ex
        traceback.print_exc()

    print "add %s new trades" % add_trades
    print "update %s existing trades" % modify_trades

def process_deliver_time(trades):
    """
    fi = open('order_20160719.json','r')
    vals = json.loads(fi.readline())
    """
    print "===============Start to process deliver time================"
    res = trades['response']['trades']
    print len(res)

    current_time = datetime.now()
    baseline_time = current_time - timedelta(7)
    baseline_time = baseline_time.strftime("%Y-%m-%d %H:%M:%S") 

    num = 0
    break_list = ['时', '.', '点', ':', '：']
    #res = [{'buyer_message':'做好随时可以送，12点前。谢谢'.decode('utf-8'), 'pay_time': '2016-07-18 11:11:28', 'title':'周一午餐'.decode('utf-8')}]
    for trade in res:
        if trade['pay_time'] < baseline_time:
            continue
        message = trade['buyer_message']
        valid = True
        bk = '$'
        for breaker in break_list:
            breaker = breaker.decode('utf-8')
            if message.count(breaker) > 1:
                valid = False
                break

            if message.count(breaker) == 1:
                bk = breaker

        if not valid or '$' == bk:
            continue
        """
        if (':' not in trade['buyer_message'] and '：'.decode('utf-8') not in trade['buyer_message']) or trade['buyer_message'].count(':') > 1 or trade['buyer_message'].count('：'.decode('utf-8')) > 1:
            continue
        """
        if '' == trade['pay_time']:
            continue
        #print trade['title']
        #print message

        num += 1
        pay_time = datetime.strptime(trade['pay_time'], "%Y-%m-%d %H:%M:%S")
        vals = message.split(bk)
        half = False
        if vals[1].startswith('半'.decode('utf-8')):
            half = True
        vals[0] = vals[0][-2:]
        vals[1] = vals[1][:2]
        left = filter(lambda ch: ch in '0123456789', vals[0])
        right = filter(lambda ch: ch in '0123456789', vals[1])
        #dTime = left + ':' + right
        #print dTime

        weekday = -1
        dish = -1
        for key in weekdays:
            if key.decode('utf-8') in trade['title']:
                weekday = weekdays[key]
                break
        for key in dishes:
            if key.decode('utf-8') in trade['title']:
                dish = dishes[key]
                break

        if '' == left:
            continue
        if int(left) > 23:
            left = int(left) % 10
        if '' == right:
            if half:
                right = 30
            else:
                right = 0

        dTime = datetime(pay_time.year, pay_time.month, pay_time.day, int(left), int(right), 0)
        #now = datetime.now()
        weekday_now = dTime.weekday()

        if False:
            print weekday_now
            print pay_time
            print weekday
            print dish

        if -1 != weekday:
            day_diff = weekday - weekday_now
            if 0 > day_diff:
                day_diff += 7
            #print "diff" + str(day_diff)
            dTime = dTime + timedelta(day_diff)

        new_left = left
        if -1 != dish:
            if 1 == dish and int(left) < 6:
                new_left = int(left) + 12
            if 2 == dish and int(left) < 13:
                new_left = int(left) + 12
            if new_left > 23:
                new_left = left

            #print left
        dTime = datetime(dTime.year, dTime.month, dTime.day, int(new_left), int(right), 0)
        #print dTime

        sql = "UPDATE trades SET deliver_time = '" + dTime.strftime("%Y-%m-%d %H:%M:%S") + "' WHERE tid = '" + trade['tid'] + "';"
        #print sql
        cur.execute(sql)

if __name__ == "__main__":
    db = MySQLdb.connect(host="localhost",    # your host, usually localhost
                     user="root",         # your username
                     passwd="chef2015L",  # your password
                     db="chef")        # name of the data base

    db.set_character_set('utf8')
    cur = db.cursor()
    cur.execute('SET NAMES utf8;')
    cur.execute('SET CHARACTER SET utf8;')
    cur.execute('SET character_set_connection=utf8;')

    current_time = datetime.now()
    hour = current_time.hour
    minute = current_time.minute

    weekdays = {'周日':6, '周一':0, '周二':1, '周三':2, '周四':3, '周五':4, '周六':5}
    dishes = {'早餐':0, '午餐':1, '晚餐':2}

    if hour > 4 and hour < 22:
        # update trade info every 5 minutes during 5:00am to 10:00pm
        get_all_trades_info()
        get_fans_helper()
    elif minute >= 0 and minute <= 4:
        get_all_trades_info()
        get_fans_helper()

    """
    if minute >= 0 and minute <= 4:
        # update user info one time per hour
        get_fans_helper(cur)
    """

    db.commit()
    db.close()

    print "=========Process Complete: %s==========" % current_time.strftime("%Y-%m-%d 00:00:00") 
