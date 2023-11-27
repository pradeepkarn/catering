<?php

// The limit of data fetch from database at an attempt
const DB_ROW_LIMIT = 100;
const FRONT_ROW_LIMIT = 10;

const USER_ROLES = array(
    'subscriber' => 'Subscriber',
    'author' => 'Author',
    'editor' => 'Editor',
    'admin' => 'Admin'
);

const ADMIN_ROLES = array(
    'subscriber' => 'Subscriber',
    'author' => 'Author',
    'editor' => 'Editor',
    'shop_manager' => 'Shop Manager'
);

const USER_GROUP = array(
    'admin' => 'admin',
    'user' => 'user',
    'caterer' => 'caterer',
);
const QR_SCAN_GROUP = [
    0=>"NA",
    1=>"Breakfast",
    2=>"Lunch",
    3=>"Dinner"
];
const FOOD_CATEGORY = [
    0=>"NA",
    1=>"Junior",
    2=>"Senior"
];
const USER_GROUP_LIST = ['employee', 'manager'];
const ADMIN_GROUP_LIST = ['superuser', 'admin', 'subadmin'];
const PERMISSION_GROUP_LIST = ['all', 'employee', 'event', 'subadmin'];
// employee positions
const POSITIONS = [
    0 => "HELPER", 1 => "MASON", 2 => "SCAFFOLDER", 3 => "ELECTRICIAN", 4 => "LAYDOWN SECURITY",
    5 => "TEA BOY", 6 => "CIVIL FOREMAN", 7 => "FLAG MAN", 8 => "CARPENTER", 9 => "PLUMBER",
    10 => "SCAFFOLDING FOREMAN", 11 => "STEEL FIXER", 12 => "CONCRETE", 13 => "ROLLER OPERATOR",
    14 => "GRADER OPERATOR", 15 => "LIGHT DRIVER", 16 => "TANKER DRIVER", 17 => "HEAVY DRIVER",
    18 => "EXCAVATOR OPERATOR", 19 => "EXCAVATION OPERATOR", 20 => "LOADER OPERATOR",
    21 => "SHOVEL OPERATOR", 22 => "COASTER DRIVER", 23 => "SURVEYOR", 24 => "HSE ENGINEER",
    25 => "HSE MANAGER", 26 => "PLANNING ENGINEER", 27 => "EQUIPMENT INSPECTOR", 28 => "HSE OFFICER", 29 => "MANAGER"
];

// const POSITIONS = [
//     "0" => "HELPER",
//     "1" => "MASON",
//     "2" => "SCAFFOLDER",
//     "3" => "ELECTRICIAN",
//     "4" => "LAYDOWN SECURITY",
//     "5" => "TEA BOY",
//     "6" => "CIVIL FOREMAN",
//     "7" => "FLAG MAN",
//     "8" => "CARPENTER",
//     "9" => "PLUMBER",
//     "10" => "SCAFFOLDING FOREMAN",
//     "11" => "STEEL FIXER",
//     "12" => "CONCRETE",
//     "13" => "ROLLER OPERATOR",
//     "14" => "GRADER OPERATOR",
//     "15" => "LIGHT DRIVER",
//     "16" => "TANKER DRIVER",
//     "17" => "HEAVY DRIVER",
//     "18" => "EXCAVATOR OPERATOR",
//     "19" => "EXCAVATION OPERATOR",
//     "20" => "LOADER OPERATOR",
//     "21" => "SHOVEL OPERATOR",
//     "22" => "COASTER DRIVER",
//     "23" => "SURVEYOR",
//     "24" => "HSE ENGINEER",
//     "25" => "HSE MANAGER",
//     "26" => "PLANNING ENGINEER",
//     "27" => "EQUIPMENT INSPECTOR",
//     "28" => "HSE OFFICER"
// ];

const RESTAURANT_API_KEY = "6SedFzPnMuFxC9L3hyLbLCJnevY+k8HAv6afu8WiQa0=";
