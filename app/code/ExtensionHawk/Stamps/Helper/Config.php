<?php
namespace ExtensionHawk\Stamps\Helper;

class Config
{

    public function getCode($type, $code = '')
    {
        $codes = $this->getCodes();
        if (!isset($codes[$type])) {
            return false;
        } elseif ('' === $code) {
            return $codes[$type];
        }
        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }

    public function getCodes()
    {
        return [
            'method' => [
                'US-FC' => [
                    'label'     =>__('First-Class Mail'),
                    'options' => [
                        'Package' => 'Package',
                        'Postcard' => 'Postcard',
                    ]
                ],
                'US-XM' => [
                    'label' => __('Priority Mail Express'),
                    'options' => [
                        'Flat Rate Envelope' => 'Flat Rate Envelope',
                        'Flat Rate Padded Envelope'     => 'Flat Rate Padded Envelope',
                        'Letter' => 'Letter',
                        'Large Envelope or Flat' => 'Large Envelope or Flat',
                        'Large Package'     => 'Large Package',
                        'Legal Flat Rate Envelope' => 'Legal Flat Rate Envelope',
                        'Package' => 'Package',
                        'Thick Envelope' => 'Thick Envelope',
                    ]
                ],
                'US-MM' => [
                    'label' => __('Media Mail Parcel'),
                    'options' => [
                        'Large Envelope or Flat' =>     'Large Envelope or Flat',
                        'Large Package' => 'Large Package',
                        'Package' => 'Package',
                        'Thick Envelope' =>     'Thick Envelope',
                    ]
                ],
                'US-LM' => [
                    'label' => __('Library Mail'),
                    'options' => [
                        'Large Envelope or Flat' =>     'Large Envelope or Flat',
                        'Package' => 'Package',
                        'Thick Envelope' => 'Thick Envelope',
                    ]
                ],
                'US-PP' => [
                    'label' => __('USPS Parcel Post'),
                    'options' => [
                        'Package' => 'Package',
                    ]
                ],
                'US-PS' => [
                    'label' => __('USPS Parcel Select'),
                    'options' => [
                        'Large Package' => 'Large Package',
                        'Oversized Package' => 'Oversized Package',
                        'Package' => 'Package',
                        'Thick Envelope' => 'Thick Envelope',
                    ]
                ],
                'US-CM' => [
                    'label' => __('USPS Critical Mail'),
                    'options' => [
                        'Package' => 'Package',
                        'custompackaging' => 'Customer Packaging'
                    ]
                ],
                'US-PM' => [
                    'label' => __('Priority Mail'),
                    'options' => [
                        'Flat Rate Box' => 'Flat Rate Box',
                        'Flat Rate Envelope' => 'Flat Rate Envelope',
                        'Flat Rate Padded Envelope'     => 'Flat Rate Padded Envelope',
                        'Letter' => 'Letter',
                        'Large Envelope or Flat' => 'Large Envelope or Flat',
                        'Large Flat Rate Box' => 'Large Flat Rate Box',
                        'Large Package' => 'Large Package',
                        'Oversized Package' => 'Oversized Package',
                        'Package' => 'Package',
                        'Regional Rate Box A' => 'Regional Rate Box A',
                        'Regional Rate Box B' => 'Regional Rate Box B',
                        'Small Flat Rate Box' => 'Small Flat Rate Box',
                        'Thick Envelope' => 'Thick Envelope',
                    ]
                ],
                'US-EMI' => [
                    'label' => __('Priority Mail Express International'),
                    'options' => [
                        'Flat Rate Envelope' =>     'Flat Rate Envelope',
                        'Flat Rate Padded Envelope'     => 'Flat Rate Padded Envelope',
                        'Large Envelope or Flat' => 'Large Envelope or Flat',
                        'Large Package' => 'Large Package',
                        'Legal Flat Rate Envelope' => 'Legal Flat Rate Envelope',
                        'Oversized Package'     => 'Oversized Package',
                        'Package' => 'Package',
                        'Thick Envelope' =>     'Thick Envelope',
                    ]
                ],
                'US-PMI' => [
                    'label' => __('Priority Mail International'),
                    'options' => [
                        'Flat Rate Box'     => 'Flat Rate Box',
                        'Flat Rate Envelope' => 'Flat Rate Envelope',
                        'Flat Rate Padded Envelope'     => 'Flat Rate Padded Envelope',
                        'Large Envelope or Flat' => 'Large Envelope or Flat',
                        'Large Flat Rate Box' => 'Large Flat Rate Box',
                        'Large Package' => 'Large Package',
                        'Legal Flat Rate Envelope' => 'Legal Flat Rate Envelope',
                        'Oversized Package'     => 'Oversized Package',
                        'Package' => 'Package',
                        'Small Flat Rate Box' => 'Small Flat Rate Box',
                        'Thick Envelope' => 'Thick Envelope',
                    ]
                ],
                'US-FCI' => [
                    'label' => __('First Class Package Service International'),
                    'options' => [
                        'Letter' =>     'Letter',
                        'Large Envelope or Flat' =>     'Large Envelope or Flat',
                        'Large Package' => 'Large Package',
                        'Oversized Package' => 'Oversized Package',
                        'Package' => 'Package',
                        'Thick Envelope' => 'Thick Envelope'
                    ]
                ],
            ],
            'container' => [
                'US-FC_Package' => 'First-Class Package',
                'US-XM_Package' => 'Priority Mail Express Package',
                'US-MM_Package' => 'Media Mail Package',
                'US-LM_Package' => 'Library Mail Package',
                'US-PP_Package' => 'Parcel Post Package',
                'US-PS_Package' => 'Parcel Select Package',
                'US-CM_Package' => 'Critical Mail Package',
                'US-PM_Package' => 'Priority Mail Package',
                'US-EMI_Package' => 'Priority Mail Express International Package',
                'US-PMI_Package' => 'Priority Mail International Package',
                'US-FCI_Package' => 'First-Class International Package',
            ],
        ];
    }
}
