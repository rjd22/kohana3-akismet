<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Akismet extends Controller {

    public function action_index()
    {
        $comment = array
        (
            'comment_author'    => 'Author',
            'comment_content'   => 'Content',
        );

        $akismet = Akismet::factory($comment)->is_spam();
        echo Kohana::debug($akismet);
    }

}
