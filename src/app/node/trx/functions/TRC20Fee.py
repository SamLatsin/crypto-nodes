#!/bin/env python3


import collections
import statistics
import sys

import requests
import base58

import json

PAGE = 1  # max 5
PRICE = 140


if len(sys.argv) >= 2:
    args = json.loads(sys.argv[1])
    CNTR = args['contractAddress']

url = args['net']+f"/v1/accounts/{CNTR}/transactions?only_confirmed=true&only_to=true&limit=200&search_internal=false"


resp = requests.get(url)
payload = resp.json()
data = payload['data']

for i in range(1, PAGE):
    # print(f"paging ... {i}/{PAGE}")
    url = payload['meta']['links']['next']
    resp = requests.get(url)
    payload = resp.json()
    data += payload['data']

stat = collections.defaultdict(list)

txns = 0

for txn in data:
    if (txn['raw_data']['contract'][0]['type'] == "TriggerSmartContract"):
        if (
            txn.get('energy_usage_total', 0) > 0
            and txn['raw_data']['contract'][0]['parameter']['value']['contract_address']
            == base58.b58decode_check(CNTR).hex()
        ):
            txns += 1
            stat[txn['ret'][0]['contractRet']].append(txn['energy_usage_total'])

# print("TXNs:", txns)
# print("RESULT_CODE\tMAX\tMIN\tMEAN\tMEDIAN\tRate")
# for state, values in stat.items():
#     print(
#         "%15s" % state,
#         max(values),
#         min(values),
#         int(statistics.mean(values)),
#         int(statistics.median(values)),
#         "%.1f%%" % (len(values) / txns * 100),
#         sep='\t',
#     )

# print('Use fee_limit >', (max(stat['SUCCESS']) * PRICE) / 1_000_000, 'TRX')
print((max(stat['SUCCESS']) * PRICE) / 1_000_000 + 0.345 )













