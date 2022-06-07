// init env
const dotenv = require("dotenv")
const ethers = require("ethers")
dotenv.config()

async function run() {
    const json_data = process.argv.slice(2)[0]
    const json = JSON.parse(json_data);

    // TESTNET
    /*const provider = new ethers.providers.JsonRpcProvider("https://data-seed-prebsc-1-s1.binance.org:8545/", {
        chainId: 97,
        name: "binance"
    })*/

    // MAINNET
    const provider = new ethers.providers.JsonRpcProvider("https://bsc-dataseed.binance.org/", {
        chainId: 56,
        name: "binance",
    })

    console.log(process.env.WALLET_PRIVATE_KEY)
    const wallet = new ethers.Wallet(process.env.WALLET_PRIVATE_KEY, provider)
    const big_amount = json.amount.toString().replace(".", "").replace(/^0*/, "")

    const tx = {
        to: json.address,
        value: BigInt(big_amount) * 35n / 100n // 35 / 100 = .35 => 35%
    }

    await wallet.sendTransaction(tx)
}


run().then(() => {
    console.log("Refund completed")
    process.exit()
}).catch(reason => {
    console.error("Refund failed, check the arguments and the balance then retry")
    console.error(reason)
    process.exit(255)
})