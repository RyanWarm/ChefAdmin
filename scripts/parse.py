#encoding=utf-8
import json

fi = open('order.txt','r')
order_obj = json.loads(fi.readline())
trades = order_obj['response']['trades']

fo = open('goods_stat.txt', 'w')
result = {}

ancle = u'\uff08'
dell = '/'
ndel = '('
for trade in trades:
	paytime = trade['pay_time'][:10]
	#print "paytime: " + paytime
	if len(paytime) == 0:
		#print trade
		continue
	paytime = paytime.replace('-', '')[:-2]
	if paytime == '201512':
		paytime = '201601'

	for order in trade['orders']:
		title = order['title']

		if ancle in title:
			title = title[:title.find(ancle)]

		if dell in title:
			title = title[:title.find(dell)]

		if ndel in title:
			title = title[:title.find(ndel)]

		#print title
		if title not in result:
			result[title] = {}

		if paytime not in result[title]:
			result[title][paytime] = 1
		else:
			result[title][paytime] += 1

fo.write(json.dumps(result))
fo.close()

fi.close()


