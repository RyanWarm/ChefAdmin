import sys
import json

fi = open('order_3.txt', 'r')
orders = json.loads(fi.readline())
trades = orders['response']['trades']
print len(trades) 

for trade in trades:
    orders = trade['orders']
    if len(orders) > 0:
        for order in orders:
            msg = order['buyer_messages']
            if len(msg) > 0:
                for m in msg:
                    if "" != m['content']:
                        print m['content']

fi.close()
