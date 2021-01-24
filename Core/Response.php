<?php
namespace Diyyick\Lib\Core;
/**
 * Description of Response
 *
 * @author Sune
 */
class Response
{
    private int $status = 200;
    protected const ALERTS = ['info', 'success', 'warning', 'danger'];
    
    // This method does the same as setStatusCode
    public function status(int $code) 
    {
        $this->status = $code;
        return $this;
    }
    
    public function toJSON(array $data=[])
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code($this->status);
        echo json_encode($data);
    }

    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    public function redirect(string $url, bool $next=false)
    {
        $protocol = $_SERVER['SERVER_PROTOCOL'];
        if (strpos($protocol, "https://")) {
            $protocol = "https://";
        } else {
            $protocol = "http://";
        }
        if ($next) {
            if ($_REQUEST['next']) {
                return header('Location: '.$_REQUEST['next'], true, 303);
            }
        }
        return header("Location: ".$protocol.$_SERVER['HTTP_HOST'].$url, true, 303);
    }
    
    public function loginRequired()
    {
        $protocol = $_SERVER['SERVER_PROTOCOL'];
        if (strpos($protocol, "https")) {
            $protocol = "https://";
        } else {
            $protocol = "http://";
        }
        $redirectLink = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if (Session::isLoggedIn() == false) {
            return $this->redirect(LOGIN.'?next='.$redirectLink);
        }
    }

    public function flash(string $type, string $message) 
   {
        if (in_array($type, self::ALERTS)) {
            $_SESSION['flashType'] = $type;
            $_SESSION['flashMessage'] = $message;
        }
    }
    
    public function render(string $view, array $args=[])
    {
        extract($args, EXTR_SKIP);
        $file = dirname(__DIR__) . "/../App/Views/$view.php";
        if (file_exists($file) && is_readable($file)){
            require_once $file;
        } else {
            echo "File not find";
        }
    }

    /*public function renderTemplate($template, $args=[])
    {
        require_once dirname(__DIR__) . "/vendor/autoload.php";
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . "/views");
        $twig = new \Twig\Environment($loader);
        return $twig->render($template, $args);
    }*/
}
