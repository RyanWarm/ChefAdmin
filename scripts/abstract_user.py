#encoding='utf-8'
import sys
import json
import codecs

fi = open('order_3.txt', 'r')
orders = json.loads(fi.readline())
trades = orders['response']['trades']
print len(trades) 

fc = codecs.open('communities.txt', 'r', 'utf-8')
coms = []
for line in fc:
    coms.append(line.replace('\n',''))

fc.close()
fo = open('user_append.json', 'w')
users = {}
for trade in trades:
    user = {}
    user['youzan_id'] = trade['weixin_user_id']
    user['address'] = trade['receiver_address']
    community = 'fake'
    for com in coms:
        if com.encode('utf-8') in user['address'].encode('utf-8'):
            community = com
            break

    if 'fake' == community:
        community = coms[0]

    user['community'] = community
    user['mobile'] = trade['receiver_mobile']
    user['name'] = trade['receiver_name']

    users[user['youzan_id']] = user

fo.write(json.dumps(users))
fo.close()

fi.close()
