/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// Boostrap Core
require('./bootstrap');


// Datatables
var dt = require( 'datatables.net' );
var buttons = require('datatables.net-buttons');

// Global
var token = document.head.querySelector('meta[name="csrf-token"]');
var timercito;

// JS Arquetipo
require('./engine');