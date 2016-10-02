<?php
// gilbert bilal
require 'FormHelper.php';


$sweets = array('puff' => 'Sesame Seed Puff',
                'square' => 'Coconut Milk Gelatin Square',
                'cake' => 'Brown Sugar Cake',
                'ricemeat' => 'Sweet Rice and Meat',
                'icecream' => 'Ice Cream');

$main_dishes = array('cuke' => 'Braised Sea Cucumber',
                     'stomach' => "Sauteed Pig's Stomach",
                     'tripe' => 'Sauteed Tripe with Wine Sauce',
                     'taro' => 'Stewed Pork with Taro',
                     'giblets' => 'Baked Giblets with Salt',
                     'abalone' => 'Abalone with Marrow and Duck Feet',
                     'Cheesepizza' => 'Cheese Pizza');
$drink = array('Coke'=> "Coke",
               'DCoke'=> "Diet Coke",
               'Spite' => "Sprite",
               'milk' => 'Milk',
               'water' => 'Water');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    list($errors, $input) = validate_form();
    if ($errors) {
        show_form($errors);
    } else {

        process_form($input);
    }
} else {

    show_form();
}

function show_form($errors = array()) {
    $defaults = array('delivery' => 'yes',
                      'size'     => 'large');

    $form = new FormHelper($defaults);


    include 'complete-form.php';
}

function validate_form( ) {
    $input = array();
    $errors = array( );

    // name is required
    if (isset($_POST['name'])) {
        $input['name'] = trim($_POST['name']);
    } else {
        $input['name'] = '';
    }
    if (! strlen($input['name'])) {
        $errors[] = 'Please enter your name.';
    }

    if (isset($_POST['email'])) {
        $input['email'] = trim($_POST['email']);

        if (false===filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)) {
            $errors[]="Email is not valid";
        }
    } else {
        $input['email'] = '';
    }
    if (! strlen($input['email'])) {
        $errors[] = 'Please enter your email.';
    }

    if(isset($_POST['size'])) {
        $input['size'] = trim($_POST['size']);
    } else {
        $input['size'] = '';
    }
    if (! in_array($input['size'], ['small','medium','large','XLarge'])) {
        $errors[] = 'Please select a size.';
    }

    if (isset($_POST['sweet'])) {
        $input['sweet'] = $_POST['sweet'];
    } else {
        $input['sweet'] = '';
    }

    if (! array_key_exists($input['sweet'], $GLOBALS['sweets'])) {
        $errors[] = 'Please select a valid sweet item.';
    }
    if (isset($_POST['drink'])) {
        $input['drink'] = $_POST['drink'];
    } else {
        $input['drink'] = '';
    }

    if (! array_key_exists($input['sweet'], $GLOBALS['sweets'])) {
        $errors[] = 'Please select a valid sweet item.';
    }

    if (isset($_POST['main_dish'])) {
        $input['main_dish'] = $_POST['main_dish'];
    } else {
        $input['main_dish'] = array();
    }
    if (count($input['main_dish']) != 2) {
        $errors[] = 'Please select exactly two main dishes.';
    } else {

        if (! (array_key_exists($input['main_dish'][0], $GLOBALS['main_dishes']) &&
               array_key_exists($input['main_dish'][1], $GLOBALS['main_dishes']))) {
            $errors[] = 'Please select exactly two valid main dishes.';
        }
    }

    if (isset($_POST['delivery'])) {
        $input['delivery'] = $_POST['delivery'];
    } else {
        $input['delivery'] = 'no';
    }
    if (isset($_POST['comments'])) {
        $input['comments'] = trim($_POST['comments']);
    } else {
        $input['comments'] = '';
    }
    if (($input['delivery'] == 'yes') && (! strlen($input['comments']))) {
        $errors[] = 'Please enter your address for delivery.';
    }

    return array($errors, $input);
}

function process_form($input) {

    $sweet = $GLOBALS['sweets'][ $input['sweet'] ];
    $main_dish_1 = $GLOBALS['main_dishes'][ $input['main_dish'][0] ];
    $main_dish_2 = $GLOBALS['main_dishes'][ $input['main_dish'][1] ];
    $drinks = $GLOBALS ['drink'] [ $input ['drink']];

    if (isset($input['delivery']) && ($input['delivery'] == 'yes')) {
        $delivery = 'do';
    } else {
        $delivery = 'do not';
    }
    $message=<<<_ORDER_
 "Thank you for your order, {$input['name']} at {$input['email']}
You requested the {$input['size']} size of $sweet, $main_dish_1, and $main_dish_2.
you would like $drinks.
You $delivery want delivery.\n

_ORDER_;
    if (strlen(trim($input['comments']))) {
        $message .= 'Your comments: '.$input['comments'];
    }

    // send the message to the chef (don't actually try to send it, uncomment for production):
    # mail('chef@restaurant.example.com', 'New Order', $message);

    // print the message, but encode any HTML entities
    // and turn newlines into <br/> tags
    print str_replace('&NewLine;', "<br />\n", htmlentities($message, ENT_HTML5));


}

