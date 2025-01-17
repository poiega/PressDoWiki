<?php
namespace PressDo;

require 'controllers/common.php';
require 'models/RecentChanges.php';

use PressDo\Models;
class WikiPage extends WikiCore
{
    public function make_data()
    {
        $page = [
            'view_name' => '',
            'title' => Lang::get('page')['RecentChanges'],
            'data' => [
                'content' => []
            ],
            'menus' => [],
            'customData' => []
        ];
        
        $lt = [
            'create' => "AND `action`='create'",
            'revert' => "AND `action`='revert'",
            'move' => "AND `action`='move'",
            'delete' => "AND `action`='delete'",
            '' => ''
        ];

        if(!isset($lt[$this->uri_data->query->logtype]))
            $ltq = '';
        else
            $ltq = $this->uri_data->query->logtype;

        $fetch = Models::RecentChanges($lt[$ltq]);
        $resultSet = [];

        foreach($fetch as $f){
            $_e = Models::get_doc_title($f['docid']);
            $rs = array(
                'document' => ['namespace' => Namespaces::get($_e['namespace']), 'title' => $_e['title']],
                'date' => $f['datetime'],
                'log' => $f['comment'],
                'author' => $f['contributor_m'],
                'ip' => $f['contributor_i'],
                'style' => null,
                'count' => $f['count'],
                'logtype' => $f['action'],
                'target_rev' => $f['reverted_version'],
                'acl' => $f['acl_changed'],
                'from' => $f['moved_from'],
                'to' => $f['moved_to'],
                'ForceShowNameSpace' => Config::get('ForceShowNameSpace'),
                'user_mode' => []
            );
            array_push($resultSet, $rs);
        }

        $page['data']['content'] = $resultSet;

        return $page;
    }
}