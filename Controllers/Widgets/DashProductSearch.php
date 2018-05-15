<?php

class Shopware_Controllers_Widgets_DashProductSearch extends Shopware_Controllers_Api_Rest
{

    public function preDispatch()
    {
        parent::preDispatch();
        $this->View()->setTemplate();
    }

    public function searchProductAction()
    {
        $db = $this->get('db');
        $search = $this->Request()->get('search');

        $searchQuery = 'SELECT ordernumber FROM s_articles_details WHERE ordernumber like "%:search:%" LIMIT 50 ';
        $searchQuery = str_replace(':search:', $search, $searchQuery);

        $ordernumbers = $db->fetchCol(
            $searchQuery
        );

        $this->View()->assign('ordernumbers', $ordernumbers);
    }

}