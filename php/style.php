<?php
    header("Content-type: text/css; charset: UTF-8");
?>

body {
    font-family: 'Rubik', sans-serif;
}

table {
    margin-top: 10px;
    border-spacing: 0;
    border: 0.5px solid black;
    border-radius: 10px;
    box-shadow: 1px 1px 3px 1px black;
    margin-left: auto;
    margin-right: auto;
}

th {
    background-color: rgb(68, 132, 206);
    color: white;
    letter-spacing: 0.7px;
    font-weight: bold;
    font-size: 18px;
}

tr {
    font-size: 16px;
    color: rgba(0, 0, 0, 0.9);
}

tr:hover {
    color: rgba(0, 0, 0, 1);
    background-color: rgba(217, 217, 217, 0.7);
}

th:first-child {
    border-top-left-radius: 10px;
}

th:last-child {
    border-top-right-radius: 10px;
}

tr:nth-child(odd) {
    background-color: rgba(217, 217, 217, 0.7);
}

td, th {
    padding: 10px 20px 10px 20px;
}

tr:last-child > td:first-child {
    border-bottom-left-radius: 10px;
}

tr:last-child > td:last-child {
    border-bottom-right-radius: 10px;
}
