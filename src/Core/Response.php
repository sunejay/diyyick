<?php
namespace Diyyick\Core;
/**
 * Description of Response
 *
 * @author Sune
 */
class Response
{
    private $status = 200;
    private const ALERTS = array('info', 'success', 'warning', 'danger');
    private const IMAGE_TYPES = array('image/jpeg', 'image/gif', 'image/png');
    
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
        if ($next) {
            if ($_GET['next']) {
                return header('Location: ' . BASEPATH . $_GET['next'], true, 303);
                exit;
            }
        }
        return header('Location: ' . BASEPATH . $url, true, 303);
        exit;
    }
    
    public function loginRequired()
    { 
        if (!Session::isLoggedIn()) {
            return $this->redirect(LOGIN . '?next=' . '/' . $_REQUEST['uri']);
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
            throw new \Exception("File not found", 404);
        }
    }

    public function renderTemplate($template, $args=[])
    {
        require_once dirname(__DIR__) . "/vendor/autoload.php";
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . "/views");
        $twig = new \Twig\Environment($loader);
        return $twig->render($template, $args);
    }

    public function saveImage($getFile)
    {
        if ($getFile['error'] != 0) {
            $this->flash('danger', 'Error uploading file');
        } else {
            if (in_array($getFile['type'], self::IMAGE_TYPES) && $getFile['size'] < 500000) {
                $filename = dirname(__DIR__) . '/../uploads/' . time() . '-' . $getFile['name'];
            } else {
                $this->flash('danger', 'Invalid file type or file size is too large.');
            }
        }
        move_uploaded_file($getFile['tmp_name'], $filename);
        return $filename;
    }
}
