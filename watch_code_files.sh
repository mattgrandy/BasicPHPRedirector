#!/bin/bash

# Directory to watch (adjust the path as needed)
WATCH_DIR="/path/to/your/directory/"

# Twilio credentials and numbers (replace these with your actual values)
TWILIO_SID="your_twilio_account_sid"
TWILIO_TOKEN="your_twilio_auth_token"
TWILIO_FROM="+1234567890"   # Your Twilio phone number
TWILIO_TO="+0987654321"     # Destination phone number

# Monitor the directory for new files being created
inotifywait -m "$WATCH_DIR" -e create --format '%f' |
while read file; do
    # Check if the file name indicates a code file (e.g., code_YYYYMMDDHHMMSS.txt)
    if [[ "$file" == code_* ]]; then
        # Optional: read the content of the file (which should be your code)
        code=$(cat "$WATCH_DIR/$file")
        
        # Prepare the message body
        message="New code file created: $file. Code: $code"
        
        # Send the SMS using Twilio's API
        curl -X POST "https://api.twilio.com/2010-04-01/Accounts/${TWILIO_SID}/Messages.json" \
             --data-urlencode "Body=${message}" \
             --data-urlencode "From=${TWILIO_FROM}" \
             --data-urlencode "To=${TWILIO_TO}" \
             -u "${TWILIO_SID}:${TWILIO_TOKEN}"
        
        echo "SMS sent for file: $file"
    fi
done
