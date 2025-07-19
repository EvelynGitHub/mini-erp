Para testar o WebHook

```
curl -X POST 'http://localhost:8080/index.php?page=webhook' \
  -H "Content-Type: application/json" \
  -d '{"id":1,"status":"cancelado"}'

curl -X POST 'http://localhost:8080/index.php?page=webhook' \
  -H "Content-Type: application/json" \
  -d '{"id":2,"status":"pago"}'

```

