Created with: https://mermaid-js.github.io

```
graph LR
  A[Action needs <br>authorization]--> B
  B[Choose code <br>delivery channel <br> eg. SMS, Email]--> C
  C[Enter recieved code]--> D
  C--> E[Resend code] --> C
  D{Verify code}--> |correct| F
  F[Execute user action<br>after successfully<br> code validation]
```

