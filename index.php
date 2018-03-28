<?php
/**
 * Created by PhpStorm.
 * User: jsking
 * Date: 3/28/18
 * Time: 9:09 AM
 */

namespace Ekuma\Administrator;
use Ekuma\Model\FoodInvoice;

class ManageFoodSubscription
{



    public function index($request, $response, $args){
// this page renders our html twig view with the pagination html
        global $container;

// so if we have $pageId set, we pass it
        $pageId = (int)(!isset($_REQUEST['pageId']) ? 1 : $_REQUEST['pageId']);

        $item = self::getPaginatedData($request, $response,$pageId,50); // 50 here is number of item to display once

//dump($item);
//return $response->withJson($item);

        return $container->view->render($response, '/admin/index.twig',['packages'=>$item,'route' => array('title'=>'Manage Subscriptions ', 'seller_assets'=>"../../../../seller_theme", "cordova_files"=>"../../../../cordova_files", 'name'=>'seller_view_manage_subscription')]);


    }


    public static function getPaginatedData($request, $response, $pageId=1,$perPage=50){
        // get data from db and return json..

        $count = FoodInvoice::where('food_subscription', '>', 0)->count();
        $page = $pageId;
        $page = ($page == 0 ? 1 : $page);
        $recordsPerPage = $perPage;
        $start = ($page - 1) * $recordsPerPage;
        $adjacents = "2";

        $prev = $page - 1;
        $next = $page + 1;
        $lastpage = ceil($count / $recordsPerPage);
        $lpm1 = $lastpage - 1;


        $item = FoodInvoice::where('food_subscription', '>', 0)->skip($start)->take($recordsPerPage)->orderBy('id', 'desc')->get();





        // for twig custom html pagination
        return array('data'=>$item,'pagination'=>self::htmlPagination($lastpage,$page,$prev,$next,$adjacents,$lpm1));


        /* // Should we need JSON PAGINATION WE USE THIS
           $item_count = FoodInvoice::where('food_subscription', '>', 0)->skip($start)->take($recordsPerPage)->get();
           $to = $start+count($item_count);
           return array('data'=>$item,'pagination'=>self::jsonPagination($page,$recordsPerPage,$start,$to,$count,$lastpage,1,$prev,$next,$lastpage,count($item_count)));
        */

    }


    public static function htmlPagination($lastpage,$page,$prev,$next,$adjacents,$lpm1){


        $pagination = "";
        if ($lastpage > 1) {
            $pagination .= "<div class='pagination'>";
            if ($page > 1)
                $pagination .= "<a href=\"#Page=" . ($prev) . "\" onClick='changePagination(" . ($prev) . ");'>&laquo; Previous&nbsp;&nbsp;</a>";
            else
                $pagination .= "<span class='disabled'>&laquo; Previous&nbsp;&nbsp;</span>";
            if ($lastpage < 7 + ($adjacents * 2)) {
                for ($counter = 1; $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination .= "<span class='current'>$counter</span>";
                    else
                        $pagination .= "<a href=\"#Page=" . ($counter) . "\" onClick='changePagination(" . ($counter) . ");'>$counter</a>";

                }
            } elseif ($lastpage > 5 + ($adjacents * 2)) {
                if ($page < 1 + ($adjacents * 2)) {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                        if ($counter == $page)
                            $pagination .= "<span class='current'>$counter</span>";
                        else
                            $pagination .= "<a href=\"#Page=" . ($counter) . "\" onClick='changePagination(" . ($counter) . ");'>$counter</a>";
                    }
                    $pagination .= "...";
                    $pagination .= "<a href=\"#Page=" . ($lpm1) . "\" onClick='changePagination(" . ($lpm1) . ");'>$lpm1</a>";
                    $pagination .= "<a href=\"#Page=" . ($lastpage) . "\" onClick='changePagination(" . ($lastpage) . ");'>$lastpage</a>";

                } elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                    $pagination .= "<a href=\"#Page=\"1\"\" onClick='changePagination(1);'>1</a>";
                    $pagination .= "<a href=\"#Page=\"2\"\" onClick='changePagination(2);'>2</a>";
                    $pagination .= "...";
                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                        if ($counter == $page)
                            $pagination .= "<span class='current'>$counter</span>";
                        else
                            $pagination .= "<a href=\"#Page=" . ($counter) . "\" onClick='changePagination(" . ($counter) . ");'>$counter</a>";
                    }
                    $pagination .= "..";
                    $pagination .= "<a href=\"#Page=" . ($lpm1) . "\" onClick='changePagination(" . ($lpm1) . ");'>$lpm1</a>";
                    $pagination .= "<a href=\"#Page=" . ($lastpage) . "\" onClick='changePagination(" . ($lastpage) . ");'>$lastpage</a>";
                } else {
                    $pagination .= "<a href=\"#Page=\"1\"\" onClick='changePagination(1);'>1</a>";
                    $pagination .= "<a href=\"#Page=\"2\"\" onClick='changePagination(2);'>2</a>";
                    $pagination .= "..";
                    for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                        if ($counter == $page)
                            $pagination .= "<span class='current'>$counter</span>";
                        else
                            $pagination .= "<a href=\"#Page=" . ($counter) . "\" onClick='changePagination(" . ($counter) . ");'>$counter</a>";
                    }
                }
            }
            if ($page < $counter - 1)
                $pagination .= "<a href=\"#Page=" . ($next) . "\" onClick='changePagination(" . ($next) . ");'>Next &raquo;</a>";
            else
                $pagination .= "<span class='disabled'>Next &raquo;</span>";

            $pagination .= "</div>";
        }


        return $pagination;

    }


    public static function jsonPagination($page,$per_page,$from,$to,$total,$lastpage,$first,$prev,$next,$last,$item_count){
        // return the json pagination detail for the front end guy..

        $data['page'] = array('current-page'=>$page,'per-page'=>$per_page, 'from'=>$from, 'to'=>$to, 'total'=>$total, 'loaded'=>$item_count, 'last-page'=>$lastpage, 'first-page'=>1);
        $data['links_id'] = array('first'=>$first,'prev'=>$prev, 'next'=>$next,'last'=>$last);

        return $data;




    }


}