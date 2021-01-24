<?php
namespace Diyyick\Lib\Core;
/**
 * Description of Pagination
 *
 * @author Sune
 */
class Pagination 
{
    private array $data;
    
    public function paginator(array $values, int $perPage) 
    {
        $totalValues = count($values);
        if (isset($_GET['page'])) {
            $currentPage = $_GET['page'];
        } else {
            $currentPage = 1;
        }
        $counts = ceil($totalValues / $perPage);
        $param = ($currentPage - 1) * $perPage;
        $this->data = array_slice($values, $param, $perPage);
        for ($index = 1; $index <= $counts; $index++) {
            $numbers[] = $index;
        }
        return $numbers;
    }
    
    public function fetchResult()
    {
        $resultValues = $this->data; 
        return $resultValues;
    }
}
