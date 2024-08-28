FROM golang:latest as builder

WORKDIR /app

RUN apt-get update && apt-get install -y unzip curl && rm -rf /var/lib/apt/lists/*

RUN curl -OL https://github.com/protocolbuffers/protobuf/releases/download/v21.12/protoc-21.12-linux-x86_64.zip \
    && unzip -o protoc-21.12-linux-x86_64.zip -d /usr/local \
    && rm -f protoc-21.12-linux-x86_64.zip

RUN go install google.golang.org/protobuf/cmd/protoc-gen-go@v1.28 \
    && go install google.golang.org/grpc/cmd/protoc-gen-go-grpc@v1.2

COPY go.mod go.sum ./

RUN go mod download

COPY . .

RUN protoc --proto_path=proto --go_out=proto --go-grpc_out=proto proto/friends.proto

RUN go mod tidy

RUN CGO_ENABLED=0 GOOS=linux go build -a -installsuffix cgo -o main .

FROM alpine:latest

RUN apk --no-cache add ca-certificates

WORKDIR /root/
COPY db/ ./db/

COPY --from=builder /app/main .
COPY --from=builder /app/proto ./proto

EXPOSE 8080

CMD ["./main"]
