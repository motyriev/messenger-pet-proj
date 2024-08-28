package db

import (
	"database/sql"
	"fmt"
	_ "github.com/lib/pq"
	"io/ioutil"
	"log"
)

const (
	host     = "friends-db"
	port     = 5432
	user     = "root"
	password = "root"
	dbname   = "app_db"
)

var db *sql.DB

func InitDB() {
	psqlInfo := fmt.Sprintf("host=%s port=%d user=%s "+
		"password=%s dbname=%s sslmode=disable",
		host, port, user, password, dbname)
	var err error

	db, err = sql.Open("postgres", psqlInfo)
	if err != nil {
		log.Fatal("Error connecting to the database:", err)
	}
	log.Println("Successfully connected to the database")

	err = db.Ping()
	if err != nil {
		log.Fatal("Error checking the database connection:", err)
	}
}

func InitSchema() {
	sqlFile, err := ioutil.ReadFile("db/create_tables.sql")
	if err != nil {
		log.Fatal("Error reading SQL script:", err)
	}

	_, err = db.Exec(string(sqlFile))
	if err != nil {
		log.Fatal("Error executing SQL script:", err)
	}
	fmt.Println("Table created successfully")
}

func CloseDB() {
	db.Close()
	log.Println("Database connection closed")
}

func Query(query string, args ...interface{}) (*sql.Rows, error) {
	rows, err := db.Query(query, args...)
	if err != nil {
		return nil, fmt.Errorf("query error: %v", err)
	}
	return rows, nil
}

func QueryRow(query string, args ...interface{}) *sql.Row {
	return db.QueryRow(query, args...)
}

func Exec(query string, args ...interface{}) (sql.Result, error) {
	result, err := db.Exec(query, args...)
	if err != nil {
		return nil, fmt.Errorf("exec error: %v", err)
	}
	return result, nil
}

func TxBegin() (*sql.Tx, error) {
	tx, err := db.Begin()
	if err != nil {
		return tx, err
	}
	return tx, nil
}

func SeedDB(sqlFile string) {
    log.Println("Running SQL seed")

    content, err := ioutil.ReadFile(sqlFile)
    if err != nil {
        log.Fatalf("Error reading SQL file: %v", err)
    }

    _, err = db.Exec(string(content))
    if err != nil {
        log.Fatalf("Error executing SQL script: %v", err)
    }

    log.Println("SQL seeding completed")
}
