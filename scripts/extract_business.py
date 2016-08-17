
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

def process_business():
    current_time = datetime.now()
    baseline_time = current_time - timedelta(7)
    baseline_time = baseline_time.strftime("%Y-%m-%d %H:%M:%S") 
    
    file_name = '/home/ubuntu/chef/data/order_' + current_time.strftime("%Y%m%d") + '.json'
    fi = open(file_name, 'r')
    print "start to process file: " + file_name
    vals = json.loads(fi.readline())
    res = vals['response']['trades']
    print len(res)

    num = 0
    result = {}
    for trade in res:
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
        payment = float(trade['payment'])
        discount_fee = float(trade['discount_fee'])
        adjust_fee = trade['adjust_fee']
        total_fee = float(trade['total_fee'])
        post_fee = float(trade['post_fee'])
        receiver_name = trade['receiver_name']

        if '' == trade['pay_time']:
            continue
        #print trade['title']
        #print message

        num += 1
        pay_time = datetime.strptime(trade['pay_time'], "%Y-%m-%d %H:%M:%S")

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

        if -1 == weekday or -1 == dish:
            continue

        total_fee = float(0)
        for order in trade['orders']:
            total_fee += float(order['total_fee'])

        dTime = datetime(pay_time.year, pay_time.month, pay_time.day)
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
            
        #print dTime

        key = dTime.strftime("%Y-%m-%d")
        if key not in result:
            result[key] = {}
        
        today = result[key]
        prefix = 'youzan_' + str(dish) + '_'

        #num
        num_key = prefix + 'num'
        if num_key not in today:
            today[num_key] = 0
        today[num_key] += 1
        
        #sum
        sum_key = prefix + 'sum'
        if sum_key not in today:
            today[sum_key] = float(0)
        today[sum_key] += (total_fee + post_fee)
        
        #discount
        discount_key = prefix + 'discount'
        if discount_key not in today:
            today[discount_key] = float(0)
        today[discount_key] += discount_fee
        
        #payment
        payment_key = prefix + 'payment'
        if payment_key not in today:
            today[payment_key] = float(0)
        today[payment_key] += payment
        
        #sql = "UPDATE trades SET deliver_time = '" + dTime.strftime("%Y-%m-%d %H:%M:%S") + "' WHERE tid = '" + trade['tid'] + "';"
        #print sql
        #cur.execute(sql)

    #print result['2016-07-30']

    for key in result:
        sql = "SELECT COUNT(*) FROM `business` WHERE `date`='%s';" % key
        print sql
        cur.execute(sql)
        nor = cur.fetchone()

        if 0 >= nor[0]: #daily business information not exists
            sql = "INSERT INTO `business` (`date`, `deliver_0_single`, `deliver_1_single`, `deliver_2_single`, `deliver_0_time`, `deliver_1_time`, `deliver_2_time`) VALUES ('" + key + "', 25, 25, 25, 3, 3, 4);"
            print sql
            cur.execute(sql)

        sql = "UPDATE business SET date = '" + key + "'"

        for i in range(3):
            num_key = 'youzan_' + str(i) + '_num'
            if num_key in result[key]:
                sql +=  ", " + num_key + " = " + str(result[key][num_key])

            sum_key = 'youzan_' + str(i) + '_sum'
            if sum_key in result[key]:
                sql +=  ", " + sum_key + " = " + str(result[key][sum_key])

            discount_key = 'youzan_' + str(i) + '_discount'
            if discount_key in result[key]:
                sql +=  ", " + discount_key + " = " + str(result[key][discount_key])

            payment_key = 'youzan_' + str(i) + '_payment'
            if payment_key in result[key]:
                sql +=  ", " + payment_key + " = " + str(result[key][payment_key])

        sql += " WHERE date = '" + key + "';"

        print sql
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
    hours_three = {'二十一':21, '二十二':22, '二十三':23}
    hours_two = {'十一':11, '十二':12, '十三':13, '十四':14, '十五':15, '十六':16, '十七':17, '十八':18, '十九':19, '二十':20}
    hours_one = {'一':1, '二':2, '三':3, '四':4, '五':5, '六':6, '七':7, '八':8, '九':9, '十':10, '两':2}
    minutes_three = {'二十一':21, '二十二':22, '二十三':23, '二十四':24, '二十五':25, '二十六':26, '二十七':27, '二十八':28, '二十九':29}
    minutes_three.update({'三十一':31, '三十二':32, '三十三':33, '三十四':34, '三十五':35, '三十六':36, '三十七':37, '三十八':38, '三十九':39})
    minutes_three.update({'四十一':41, '四十二':42, '四十三':43, '四十四':44, '四十五':45, '四十六':46, '四十七':47, '四十八':48, '四十九':49})
    minutes_three.update({'五十一':51, '五十二':52, '五十三':53, '五十四':54, '五十五':55, '五十六':56, '五十七':57, '五十八':58, '五十九':59})
    minutes_two = {'零一':1, '零二':2, '零三':3, '零四':4, '零五':5, '零六':6, '零七':7, '零八':8, '零九':9, '十分':10, '一刻':15}
    minutes_two.update({'十一':11, '十二':12, '十三':13, '十四':14, '十五':15, '十六':16, '十七':17, '十八':18, '十九':19})
    minutes_two.update({'二十':20, '三十':30, '四十':40, '五十':50})
    minutes_one = {'半':30}
    adjust_strs = ['尽快', '马上', '立刻']

    process_business()

    db.commit()
    db.close()

    print "=========Process Complete: %s==========" % current_time.strftime("%Y-%m-%d %H:%M:%S") 
