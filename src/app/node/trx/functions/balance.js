const TronWeb = require('tronweb')

function balance(args) {
    args = JSON.parse(args);
    const tronWeb = new TronWeb({
        fullHost: args.net,
        headers: { "TRON-PRO-API-KEY": args.apiKey }
    })
    tronWeb.trx.getBalance(args.address).then(result => console.log(result));
}
const args = process.argv.slice(2)
balance(args[0]);