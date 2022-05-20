# csv parser
> A small csv parser project

## Table of contents
* [General info](#general-info)
* [Technologies](#technologies)
* [Status](#status)

## General info
A basic csv parser that runs on a terminal.
It should show on screen readable info about each item in the csv, then create a file with
unique combinations of each item.

To run, open cmd and run with "php parser.php --file example.csv --unique-combinations=outputFile.csv".
--uniquecombinations is optional, if none given a default file will be created in the same directory as
the application.

## Features
- read csv file (json, xml etc in the future).
- Create a file containing only unique combinations (with a count).

## Technologies
- PHP

## Status
Project is: _in progress_