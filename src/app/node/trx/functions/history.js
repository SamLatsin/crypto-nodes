
const TronWeb = require('tronweb')
const TronGrid = require("trongrid");

function history(args) {
    args = JSON.parse(args);
    const tronWeb = new TronWeb({
        fullHost: args.net,
        headers: { "TRON-PRO-API-KEY": args.apiKey }
    })
    const tronGrid = new TronGrid(tronWeb);
    const options = {
        // onlyTo: true,
        // onlyConfirmed: true,
        limit: 200,
        orderBy: 'timestamp,asc',
        // minBlockTimestamp: Date.now() - 60000 // from a minute ago to go on
    };
    tronGrid.account.getTransactions(args.address, options).then(transactions => {
    	transactions.data.forEach(function(data, index) {
    	  toConvert = transactions.data[index].raw_data.contract[0].parameter.value.owner_address;
    	  transactions.data[index].raw_data.contract[0].parameter.value.owner_address = TronWeb.address.fromHex(toConvert);
    	  toConvert = transactions.data[index].raw_data.contract[0].parameter.value.to_address;
    	  transactions.data[index].raw_data.contract[0].parameter.value.to_address = TronWeb.address.fromHex(toConvert);
	    });
        console.log(JSON.stringify(transactions));
    }).catch(err => console.error(err));
}
const args = process.argv.slice(2)
history(args[0]);

