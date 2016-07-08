#!/usr/bin/python
import MySQLdb
import json
import sys

db = MySQLdb.connect(host="localhost",    # your host, usually localhost
                     user="root",         # your username
                     passwd="chef2015L",  # your password
                     db="chef")        # name of the data base

# you must create a Cursor object. It will let
#  you execute all the queries you need
cur = db.cursor()

# Use all the SQL you like
#cur.execute("SELECT * FROM users")

# print all the first cell of all the rows
"""
for row in cur.fetchall():
    print row[1]
"""

fi = open('users.txt', 'r')
data = fi.read()
users = json.loads(data)
print len(users['users'])

for user in users['users']:
    youzan_id = user['user_id']
    open_id = user['weixin_openid']
    
    sql = "UPDATE users SET youzan_id = '" + youzan_id + "' WHERE open_id = '" + open_id + "'"
    print sql

    cur.execute(sql)

db.close()
