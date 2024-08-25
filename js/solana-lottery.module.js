import {
    clusterApiUrl,
    Connection,
    LAMPORTS_PER_SOL,
    PublicKey,
    SystemProgram,
    Transaction,
} from "@solana/web3.js";
import {
    ActionGetResponse,
    ACTIONS_CORS_HEADERS,
    ActionPostRequest,
    createPostResponse,
    ActionPostResponse,
} from "@solana/actions";

document.getElementById('buy-ticket').addEventListener('click', async () => {
    try {
        const account = new PublicKey(prompt("Enter your Solana account public key:"));

        let ticketCount = parseInt(prompt("Enter number of tickets to buy (default is 1):")) || 1;
        const ticketPrice = 0.01;
        const totalAmount = ticketPrice * ticketCount;

        const connection = new Connection(clusterApiUrl("mainnet-beta"));
        const TO_PUBKEY = new PublicKey("4gM2BHWUeism4D6BMEiBUjFN24XBjAhf46wYezCPy1Ef");

        const transaction = new Transaction().add(
            SystemProgram.transfer({
                fromPubkey: account,
                lamports: totalAmount * LAMPORTS_PER_SOL,
                toPubkey: TO_PUBKEY,
            })
        );

        transaction.feePayer = account;
        transaction.recentBlockhash = (await connection.getLatestBlockhash()).blockhash;

        const payload = await createPostResponse({
            fields: {
                transaction,
                message: `You have bought ${ticketCount} ticket(s)! Good luck!`,
            },
        });

        alert(payload.fields.message);
    } catch (err) {
        console.error("Error details:", err);
        alert("An error occurred: " + err.message);
    }
});
