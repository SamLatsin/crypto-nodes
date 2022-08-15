const TronWeb = require('tronweb')

function getInfo(args) {
    args = JSON.parse(args);
    const tronWeb = new TronWeb({
        fullHost: args.net,
        headers: { "TRON-PRO-API-KEY": args.apiKey }
    })
    tronWeb.trx.getNodeInfo().then(result => console.log(JSON.stringify(result)));
}
const args = process.argv.slice(2)
getInfo(args[0]);