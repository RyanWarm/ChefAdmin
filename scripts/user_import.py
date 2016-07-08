#!/usr/bin/python
# -*- coding: utf-8 -*-
import MySQLdb
import json
import sys

db = MySQLdb.connect(host="localhost",    # your host, usually localhost
                     user="root",         # your username
                     passwd="chef2015L",  # your password
                     db="chef")        # name of the data base

db.set_character_set('utf8')
cur = db.cursor()
cur.execute('SET NAMES utf8;')
cur.execute('SET CHARACTER SET utf8;')
cur.execute('SET character_set_connection=utf8;')

# you must create a Cursor object. It will let
#  you execute all the queries you need

# Use all the SQL you like
#cur.execute("SELECT * FROM users")

# print all the first cell of all the rows
"""
for row in cur.fetchall():
    print row[1]
"""

fi = open('order_3.txt', 'r')
data = fi.read()
trades = json.loads(data)
print len(trades['response']['trades'])

trades_info = open('trands_table.sql','w')

for trade in trades['response']['trades']:
    consign_time = trade['consign_time']
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
    
    """
    sql = "UPDATE users SET mobile = '" + mobile + "'"
    if len(receiver_name) > 0:
        sql += ", name = '" + receiver_name + "'"
    
    sql +=  "WHERE youzan_id = '" + youzan_id + "'"
    print sql
    cur.execute(sql)
    """

    """
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

    trades_info.write(sql.encode('utf-8') + '\n')
    cur.execute(sql)
    """
    
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

        print sql
        cur.execute(sql)

trades_info.close()
db.commit()

db.close()
