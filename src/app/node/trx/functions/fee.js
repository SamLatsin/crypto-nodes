const TronWeb = require('tronweb')

function fee(args) {
    args = JSON.parse(args);
    const tronWeb = new TronWeb({
        fullHost: args.net,
        headers: { "TRON-PRO-API-KEY": args.apiKey }
    })
    tronWeb.transactionBuilder.sendTrx(args.toAddress, args.amount, args.fromAddress).then(transaction => {
        tronWeb.trx.sign(transaction, args.privateKey).then(signedTransaction => {
            tronWeb.trx.getBandwidth(args.fromAddress).then(result => {
                bandwidth = signedTransaction.raw_data_hex.length;
                if (parseInt(result) < parseInt(bandwidth)) {
                    console.log(parseFloat(bandwidth) / 1000);
                }
                else {
                    console.log(0);
                }
            });
        });
    });
}
const args = process.argv.slice(2)
fee(args[0]);