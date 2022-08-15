const TronWeb = require('tronweb')

function balance(args) {
    args = JSON.parse(args);
    const tronWeb = new TronWeb({
        fullHost: args.net,
        headers: { "TRON-PRO-API-KEY": args.apiKey }
    })
    const address = args.address;
    const trc20ContractAddress = args.contractAddress;
    tronWeb.contract().at(trc20ContractAddress).then(contract => {
		tronWeb.setAddress(trc20ContractAddress);
		contract.balanceOf(address).call().then(result => {
			if (result._isBigNumber) {
				result = result._hex;
				result = tronWeb.toBigNumber(result);
				result = result.toNumber();
				// result = parseFloat(result) / 1e18; // testnet
				result = parseFloat(result) / 1e6; // mainnet
				console.log(result)
			}
		});
	});
}
const args = process.argv.slice(2)
balance(args[0]);