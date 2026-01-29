const express = require("express");
const bodyParser = require("body-parser");
const cors = require("cors");
const twilio = require("twilio");

const app = express();
app.use(cors());
app.use(bodyParser.json());

// Twilio credentials (keep these secure!)
const accountSid = "AC635fa2116e5f5953bea46ab25ed88ccd";
const authToken = "85df249639843ce53be8e969a19e7d70";
const client = twilio(accountSid, authToken);
const fromNumber = "+17198004795"; // Twilio number

app.post("/send-sms", async (req, res) => {
    const { to, message } = req.body;

    try {
        const response = await client.messages.create({
            body: message,
            from: fromNumber,
            to: to
        });
        res.status(200).send({ success: true, sid: response.sid });
    } catch (error) {
        console.error("Twilio Error:", error.message);
        res.status(500).send({ success: false, error: error.message });
    }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => console.log(`Twilio SMS server running on port ${PORT}`));
