<?php
class CrewController {
    public function index() {
        require_once __DIR__ . '/../Models/User.php';
        require_once __DIR__ . '/../Models/ProjectMember.php';
        require_once __DIR__ . '/../Models/RoleUser.php';
        $userModel = new User();
        $projectMemberModel = new ProjectMember();
        $roleUserModel = new RoleUser();

        $crew = $userModel->getCrewMembers();
        foreach ($crew as &$member) {
            $member['projects'] = $projectMemberModel->getByUserId($member['id']);
            $member['roles'] = $roleUserModel->getByUserId($member['id']);
        }
        unset($member); // foarte important!

        require __DIR__ . '/../Views/crew/index.php';
    }
}