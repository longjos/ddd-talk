<?php
namespace Service\Tax;
class TaxRate {
    public function getTaxStrategyFor(\Model\StoreLocation $store){
        $taxParameters = $this->lookupTaxRatesForZipCode($store->zipCode);
        $taxStrategy = NULL;
        switch($taxParameters['plan']){
            case 'simple':
                $taxStrategy = new \Strategy\SingleTaxCalculation(
                    $taxParameters['rates']['t-01']
                );
                break;
            case 'complex':
                $taxStrategy = new \Strategy\CategoryTaxCalculation(
                    $this->mapTaxIds($taxParameters['rates'])
                );
        }
        return $taxStrategy;
    }

    protected function mapTaxIds($taxRates){
        $taxMap = array(
            'base' => 't-01',
            'food' => 't-02',
            'alcohol' => 't-03'
        );

        $mappedRates = array();
        foreach($taxMap as $category => $taxCode){
            $mappedRates[$category] = $taxRates[$taxMap[$category]];
        }
        return $mappedRates;
    }

    protected function lookupTaxRatesForZipCode($zipCode){
        $taxConfigResponse = json_decode(
            file_get_contents(dirname(__FILE__) . "/tax_config.json"), TRUE
        );
        return $taxConfigResponse[$zipCode];
    }
}