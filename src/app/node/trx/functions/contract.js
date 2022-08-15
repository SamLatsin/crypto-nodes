const TronWeb = require('tronweb')

function createTransaction(args) {
    args = JSON.parse(args);
    const tronWeb = new TronWeb({
        fullHost: args.net,
        headers: { "TRON-PRO-API-KEY": args.apiKey }
    })
    const trc20ContractAddress = args.contractAddress;
    tronWeb.contract().at(trc20ContractAddress).then(contract => {
        tronWeb.setAddress(trc20ContractAddress);
        tronWeb.setPrivateKey(args.privateKey);
        // console.log(args);

        // finalAmount = tronWeb.toBigNumber(parseFloat(args.amount) * 1e18); // testnet
        finalAmount = tronWeb.toBigNumber(parseFloat(args.amount) * 1e6); // mainnet

        tronWeb.transactionBuilder.triggerSmartContract(
            trc20ContractAddress, 'transfer(address,uint256)', {
            },
            [{
                type: 'address',
                value: args.toAddress
            }, {
                type: 'uint256',
                value: finalAmount.toString(10)
            }]
        ).then(transaction => {
            transaction = transaction['transaction'];
            if (args.memo) {
                tronWeb.transactionBuilder.addUpdateData(transaction, args.memo, "utf-8").then(transactionM => {
                    tronWeb.trx.sign(transactionM, args.privateKey).then(signedTransaction => {
                        tronWeb.trx.sendRawTransaction(signedTransaction).then(result => {
                            console.log(JSON.stringify(result));
                        });
                    });
                });

            }
            else {
                tronWeb.trx.sign(transaction, args.privateKey).then(signedTransaction => {
                    tronWeb.trx.sendRawTransaction(signedTransaction).then(result => {
                        console.log(JSON.stringify(result));
                    });
                });
            }
        });
    });
}
const args = process.argv.slice(2)
createTransaction(args[0]);




