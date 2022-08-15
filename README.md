# crypto-nodes
Backend API for working with the most famous cryptocurrencies: Bitcoin, Ethereum, Zcash, TRON, Monero and Dash.
## Overview
Aggregator of the most famous cryptocurrencies written in [PHP](https://www.php.net) [Phalcon](https://phalcon.io/en-us), [Python](https://www.python.org) and [JavaScript](https://developer.mozilla.org/en-US/docs/Web/JavaScript). 
### Use cases
* Crypto P2P exchange
* Online marketplace
* Banking
* Personal purposes like cold wallet
### Features
For all curriencies:
* Create wallets
* Check balance
* Generate new address
* Check node status
* Network fee calculation
* Send cryptocurrency
* Get history of transactions
* Wallet recover with progress status
* Get transaction info
* Node auto restart on fail or boot

Bitcoin special features:
* Multiple wallets import by private key or mnemonic
* Cron task for checking imported wallets and sending all bitcoins to one wallet from them

Zcash special features:
* Support for transparent addresses (t-address)
* Support for shielded addresses (z-address)

Ethereum special feature:
* Support for ERC20 USDT tokens

Tron special feature:
* Support for TRC20 USDT tokens

### What needs to be done
* Remove external API dependency for Tron and Ethereum networks
* Add new currencies
### Requirements
* Linux server
* At least 2 TB SSD
* At least 16 GB RAM
* [Phalcon framework](https://github.com/phalcon/cphalcon.git)
* [Bitcoind](https://github.com/bitcoin/bitcoin.git)
* [Geth](https://github.com/ethereum/go-ethereum.git)
* [Zcashd](https://github.com/zcash/zcash.git)
* [Monerod](https://github.com/monero-project/monero.git)
* [Dashd](https://github.com/dashpay/dash.git)
* [etherscan.io API token](https://etherscan.io/apis)
* [Zcash-mini](https://github.com/FiloSottile/zcash-mini.git)
* [Bitcoin Explorer](https://github.com/libbitcoin/libbitcoin-explorer.git)
* [hd-wallet-derive](https://github.com/dan-da/hd-wallet-derive.git)
* [Tron API key](https://developers.tron.network/reference/api-key)

npm requirments:
* tron-wallet-hd
* bip39
* ethereum-hdwallet
* readline
* ethereumjs-tx
* web3
* tronweb
* trongrid

python requirments:
* base58
* requests
* json
* sys
* statistics
* collections
### Installation
## License

Chat is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

