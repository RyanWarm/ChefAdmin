import sys
import json

fi = open('goods_stat.txt', 'r')
goods = json.loads(fi.readline())

fo = open('goods_refine.txt', 'w')
result = []

for title in goods:
	good = goods[title]
	tmp = {}
	tmp['articles'] = []
	
	total = 0
	for order in good:
		tmp['articles'].append([order, good[order]])
		total += good[order]

	tmp['total'] = total
	tmp['name'] = title + '(' + str(total) + ')'
	result.append(tmp)

result = sorted(result, key=lambda good: good['total'], reverse=True)

fo.write(json.dumps(result))
fo.close()
print len(result)

fi.close()
