const TronWeb = require('tronweb')

function createTransaction(args) {
    args = JSON.parse(args);
    const tronWeb = new TronWeb({
        fullHost: args.net,
        headers: { "TRON-PRO-API-KEY": args.apiKey }
    })
    tronWeb.transactionBuilder.sendTrx(args.toAddress, args.amount, args.fromAddress).then(transaction => {
        if (args.memo) {
            tronWeb.transactionBuilder.addUpdateData(transaction, String(args.memo)).then(transactionM => {
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
        
    }).catch(err => console.error(err))
}
const args = process.argv.slice(2)
createTransaction(args[0]);