openssl genrsa -out sharingService.pem 2048

openssl rsa -in sharingService.pem -pubout -out publickey.crt
