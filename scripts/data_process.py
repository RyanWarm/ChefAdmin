
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
import MySQLdb
import json
import sys
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

    start_date = date(2015, 10, 18)
    end_date = date(current_time.year, current_time.month, current_time.day)
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
    fo = open('users_' + current_time.strftime("%Y%m%d") + '.json', 'w')
    fo.write(json.dumps(result))
    fo.close()

    users_injection(result)

    return result

def daterange(start_date, end_date):
    for n in range(int ((end_date - start_date).days)):
        yield start_date + timedelta(n)

def users_injection(users):
    try:
        for user in users['users']:
            if 'follow_time' not in user_info or user_info['follow_time'] is None:
                TIMEFORMAT = '%Y-%m-%d'
                join_time = time.strftime(TIMEFORMAT, time.localtime(time.time()))
            else:
                join_time = user_info['follow_time'][:10]

            open_id = user_info['weixin_openid']
            youzan_id = user['user_id']
            alias = user_info['nick'].encode("utf-8")
            sex = user_info['sex']
            traded_num = user_info['traded_num']
            traded_money = user_info['traded_money']
            points = user_info['points']
            avatar = user_info['avatar']

            sql = "SELECT COUNT(*) FROM `users` WHERE `open_id`='%s'" % open_id
            result = cur.execute(sql).fetchone()
            nor = result[0]

            add_users = 0
            update_users = 0
            if 0 < nor: #user information exists
                sql = "UPDATE users SET traded_num = " + str(traded_num)
                sql += ", traded_money = " + str(traded_money)
                sql += ", points = " + str(points)
                sql += "WHERE open_id = '" + open_id + "'"
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

            print sql
            cur.execute(sql)
    except Exception,ex:
        print Exception,":",ex

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
    db = MySQLdb.connect(host="localhost",    # your host, usually localhost
                     user="root",         # your username
                     passwd="chef2015L",  # your password
                     db="chef")        # name of the data base

    db.set_character_set('utf8')
    cur = db.cursor()
    cur.execute('SET NAMES utf8;')
    cur.execute('SET CHARACTER SET utf8;')
    cur.execute('SET character_set_connection=utf8;')

    #get_all_trades_info()
    get_fans_helper()

    db.close()
