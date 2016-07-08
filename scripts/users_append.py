#!/usr/bin/python
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

fi = open('user_append.json', 'r')
data = fi.read()
users = json.loads(data)
print len(users)

for uid in users:
    user = users[uid]
    sql = "UPDATE users SET name = '" + user['name'] + "', mobile = '" + user['mobile'] + "', community = '" + user['community'] + "', address = '" + user['address'] + "' WHERE youzan_id = '" + user['youzan_id'] + "'"
    print sql

    cur.execute(sql)

db.close()
