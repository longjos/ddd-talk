<?php
namespace Strategy;

interface ITaxCalculation {
    function calculateTax(\Model\WindowOrder $order);
};