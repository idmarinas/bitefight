<?php
/**
 * Created by PhpStorm.
 * User: osein
 * Date: 14/01/17
 * Time: 03:26
 */

class UserController extends GameController
{

    public function getProfile() {
        $highscorePosition = ORM::for_table('user')
            ->where_gt('s_booty', $this->user->s_booty)
            ->count() + 1;

        $this->view->highscorePosition = $highscorePosition;

        $this->view->str_cost = getSkillCost($this->user->str);
        $this->view->def_cost = getSkillCost($this->user->def);
        $this->view->dex_cost = getSkillCost($this->user->dex);
        $this->view->end_cost = getSkillCost($this->user->end);
        $this->view->cha_cost = getSkillCost($this->user->cha);

        $this->view->pick('user/profile');
    }

    public function getProfileLogo() {
        $this->view->pick('user/logo');
    }

    public function postProfileLogo() {
        if ($this->security->checkToken()) {
            $gender = $this->request->getPost('gender');
            $type = $this->request->getPost('type');

            $this->user->gender = $gender;
            $this->user->image_type = $type;
            $this->user->save();

            return $this->response->redirect(getUrl('user/profile'));
        } else {
            return $this->dispatcher->forward(array(
                'controller' => 'error',
                'action'     => 'show404',
            ));
        }
    }

    public function getNews() {
        $this->view->menu_active = 'news';
        $news = ORM::for_table('news')
            ->find_many();

        $this->view->news = $news;
        $this->view->pick('home/news');
    }

}