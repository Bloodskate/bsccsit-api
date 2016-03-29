<?php
header("Access-Control-Allow-Origin: *");
/** 
* API for bsccsit app
*
* @author Prasanna Mishra
*/

require_once 'vendor/autoload.php';
require_once 'app/config.php';



$app = new \Slim\Slim();

////////// test //////////
$app->get('/', function(){
	echo 'HI';
});


/*---------------------------------*/
/*---------------------------------*/
/*        methods on User          */
/*---------------------------------*/
/*---------------------------------*/

////////// method to get all users //////////
/** 
 * url    - /allusers
 * method - GET
 * params 
 */

$app->get('/allusers', function () use ($app) {
    
    $users = User::all();
    echoResponce($users);
   
});

////////// method to update or register a user ; update if existing //////////
/**
 * url     -    /login   
 * method  -    POST
 * params  -    user_id, name, email, phone number, semester, college, gender, location    
 */

$app->post('/login', function() use ($app){

    $id             =       $app->request->post('user_id');
    $name           =       $app->request->post('name');
    $email          =       $app->request->post('email');
    $phone_number   =       $app->request->post('phone_number');
    $semester       =       $app->request->post('semester');
    $college        =       $app->request->post('college');
    $gender         =       $app->request->post('gender');
    $location       =       $app->request->post('location');
    
    $user = User::updateOrCreate(['id' => $id],[
        'id'            =>       $id,
        'name'          =>       $name,
        'email'         =>       $email,
        'phone_number'  =>       $phone_number,
        'semester'      =>       $semester,
        'college'       =>       $college,
        'gender'        =>       $gender,
        'location'      =>       $location
    ]);

    if($user->exists){
        if($user->wasRecentlyCreated){
            $responce['error'] = false;
            $responce['data'] = 'Success';
        } else {
            $responce['error'] = false;
            $responce['data'] = 'Success';
        }
    } else {
            $responce['error'] = true;
            $responce['data'] = 'Error';
    }
    echoResponce($responce);

});

////////// method to get data of an existing user//////////
/**
 * url     -    /getuser   
 * method  -    POST
 * params  -    user_id  (fbid of required user)
 */

/*!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!middleware!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/

// - check user hatayo ani temporarily app chalne banayo


$app->post('/getuser', 'checkuser', function() use ($app){
    $id = $app->request->post('user_id');

    $user = User::find($id);
    $responce = [];
    if($user){
        $responce['error'] = false;
        $responce['data'] = $user;
    } else {
        $responce['error'] = true;
        $responce['data'] = null;
    }
    echoResponce($responce);

});

//////////method to get profile of a user*/////////
/**
 * url    - /getprofile
 * method - POST
 * params - user_id (fbid os the user)
 */

$app->post('/getprofile' , 'checkuser' , function () use ($app){

    $id = $app->request->post('user_id');
    $user = User::find($id);
    $result = [];
    $result['user'] = $user;
    $own_projects = $user->ownProjects()->get();
    if($own_projects){
        $result['admin_projects'] = $own_projects;
    }
    $projects = $user->projects()->get();
    if($projects){
        $result['member_projects'] = $projects;
    }
    $responce['error'] = false;
    $responce['data'] = $result;
    echoResponce($responce);

});


////////// method to get communities of an existing user//////////
/**
 * url     -    /getusercommunities   
 * method  -    POST
 * params  -    user_id (fbid of the user) 
 */


$app->post('/getusercommunities', 'checkuser',  function() use ($app){

        $id = $app->request->post('user_id');

        $user = User::find($id);

        $communities = explode(',', $user->communities);

        $responce['error'] = false;
        $responce['data'] = $communities;
        echoResponce($responce);      

});

////////// method to add communities to an existing user//////////
/**
 * url     -    /updateusercommunities   
 * method  -    POST
 * params  -    user_id (fbid of the user)  , communities (fbid of the communities, seperated by a comma ',' )
 */


$app->post('/updateusercommunities', 'checkuser', function() use ($app){

        $id = $app->request->post('user_id');
        $communities = $app->request->post('communities');

        $user = User::find($id);
        $user->communities = $communities;
        $user->save();

        $confirmation = User::where('communities', '=', $communities);
        if($confirmation){                    
            $responce['error'] = false;
            $responce['data'] = 'Success';
        } else{     
            $responce['error'] = true;
            $responce['data'] = 'Error';
        }
        echoResponce($responce);
});


////////// method to add reg_id to an existing user//////////
/**
 * url     -    /addregid   
 * method  -    POST
 * params  -    user_id (fbid of the user)  , reg_id (gcm id)
 */


$app->post('/addregid', 'checkuser', function() use ($app){

        $id = $app->request->post('user_id');
        $reg_id = $app->request->post('reg_id');

        $user = User::find($id);
        $user->reg_id = $reg_id;
        $user->save();

        $confirmation = User::where('reg_id', '=', $reg_id);
        if($confirmation){
            echo 'Success';
        } else{
            echo 'error, request not completed';
        }

});

////////// method to update user semester/////////
/**
 * url     -    /updateusersemester  
 * method  -    POST
 * params  -    user_id (fbid of the user)  , semester
 */



$app->post('/updateusersemester', 'checkuser', function() use ($app){

    $id = $app->request->post('user_id');
    $semester = $app->request->post('semester');

    $user = User::find($id);
    $user->semester = $semester;
    $user->save();

    $confirmation = User::where('semester', '=', $semester);
    if($confirmation){
        $responce['error'] = false;
        $responce['data'] = 'Successss';
    } else{            
        $responce['error'] = true;
        $responce['data'] = 'Error';
    }
    echoResponce($responce);
});


/*---------------------------------*/
/*---------------------------------*/
/*      methods on elibrary        */
/*---------------------------------*/
/*---------------------------------*/


////////// method to all the books of a semester//////////
/**
 * url     -    /getelibrary   
 * method  -    POST
 * params  -    semester (semester whose notes are needed)
 */

$app->post('/getelibrary', function() use ($app){

    $semester =  $app->request->post('semester');

    $books = Elibrary::where('semester', '=', $semester)->get();

    $responce['error'] = false;
    $responce['data'] = $books;
    echoResponce($responce);
});


////////// method to get all books//////////
/**
 * url     -    /elibrary   
 * method  -    GET
 * params  -    
 */
$app->get('/elibrary', function() use ($app){

    $books = Elibrary::all();

    echoResponce($books);

});


/*---------------------------------*/
/*---------------------------------*/
/*     methods on Community        */
/*---------------------------------*/
/*---------------------------------*/


////////// method to get all communities//////////
/**
 * url     -    /allcommunities   
 * method  -    GET
 * params  -    
 */
$app->get('/allcommunities', function() use ($app){

    $communities = Community::all();

    echoResponce($communities);

});


////////// method to add a community//////////
/**
 * url     -    /addcommunity   
 * method  -    POST
 * params  -    comm_id (fbid of the community) , title (name of the community) , isVerified (verification status - 0 or 1) , extra (extra info)
 */


$app->post('/addcommunity','checkcommunity', function() use ($app){

    $id             =       $app->request->post('comm_id');
    $title          =       $app->request->post('title');
    $isVerified     =       $app->request->post('isVerified');
    $extra          =       $app->request->post('extra');

    $community = Community::create([
        'id'            =>      $id,
        'title'         =>      $title,
        'isverified'    =>      $isVerified,
        'extra'         =>      $extra
    ]);

    if($community->exists){
        $responce['error'] = false;
        $responce['data'] = 'Success';
    } else {
        $responce['error'] = true;
        $responce['data'] = 'Error';
    }
    echoResponce($responce);
});


////////// method to delete a community//////////
/**
 * url     -    /deletecommunity   
 * method  -    POST
 * params  -    comm_id (fbid of the community)  
 */

$app->post('/deletecommunity','checkcommunity', function() use ($app){

    $id = $app->request->post('comm_id');

    $community = Community::find($id);

    $community->delete();

    $confirmation = Community::find($id);

    if(!$confirmation){
        $responce['error'] = false;
        $responce['data'] = 'Success';
    } else{
        $responce['error'] = true;
        $responce['data'] = 'Error';
    }
    echoResponce($responce);
});


/*---------------------------------*/
/*---------------------------------*/
/*     methods on Projects         */
/*---------------------------------*/
/*---------------------------------*/

////////// method to get all projects //////////

/**
 * url    - /allprojects
 * method - GET
 * params - 
 */

$app->get('/allprojects', function() use ($app){

    $projects = Project::all()->reverse();
    $result = [];
    foreach($projects as $project){
        $project_with_tags = returnwithtags($project, $project->id);
        array_push($result, $project_with_tags);
    }
    echoResponce($result);
    
});

////////// method to add a project //////////
/**
 * url    - /addproject
 * method - POST
 * params - 
 */


$app->post('/addproject', function() use ($app){

    $user_id            =       $app->request->post('user_id');
    $title              =       $app->request->post('title');
    $description        =       $app->request->post('description');
    $required_users     =       $app->request->post('required_users');

    $project = Project::create([
            'user_id'           =>      $user_id,
            'title'             =>      $title,
            'description'       =>      $description,
            'required_users'    =>      $required_users
    ]);
    
    if($project->exists){
        $responce['error'] = false;
        $responce['data'] = 'Success';
    } else{
        $responce['error'] = true;
        $responce['data'] = 'Error';
    } 
    
    attachtags($project->id);
    echoResponce($responce);
});

////////// method to edit a project /////////

/**
 * url    - /updateproject
 * method - POST
 * params - json obj
 */

$app->post('/updateproject', function() use ($app){

    $id                 =       $app->request->post('id');
    $title              =       $app->request->post('title');
    $description        =       $app->request->post('description');
    $required_users     =       $app->request->post('required_users');

    $project = Project::find($id);
    $project->title = $title;
    $project->description = $description;
    $project->required_users = $required_users;
    $project->title = $title;
    $project->tags()->detach();
    attachtags($id);
    $project->save();
    $responce['error'] = false;
    $responce['data'] = 'success';    
    echoResponce($responce);
    
    $ids = [];
    $users = $project->users()->get();
    foreach ( $users as $user) {
        if($user->id != $project->user_id){
            $ids[] = $user->reg_id;
        }
    }
    $data = [
        'title' => $project->title,
        'message' => 'This project was recently updated',
        'tags' => 'project',
        'link' => 'brainants://bsccsit/eachproject?project_id='. $project->id
    ]; 
    sendGCM($data, $ids); 
});

/*---------------------------------*/
/*---------------------------------*/
/*       methods on Notices        */
/*---------------------------------*/
/*---------------------------------*/

////////// method to get all notices //////////

/**
 * url    - /allnotices
 * method - GET
 * params - 
 */

$app->get('/allnotices', function() use ($app){

    $notices = Notice::where('id', '>', 0)->orderBy('time', 'desc')->get();
    echoResponce($notices);
});

////////// method to get add notices //////////

/**
 * url    - /addnotice
 * method - POST
 * params - title, notice, time
 */

$app->post('/addnotice', function() use ($app){

    $title              =       $app->request->post('title');
    $notice             =       $app->request->post('notice');
    $time               =       $app->request->post('time');

    $project = Notice::create([
            'title'             =>      $title,
            'notice'            =>      $notice,
            'time'              =>      $time
    ]);

    $confirmation = Notice::where('title', '=', $title);

    if(!$confirmation){
            $responce['error'] = false;
            $responce['data'] = 'Success';
    } else{
            $responce['error'] = true;
            $responce['data'] = 'Error';
    }
    echoResponce($responce);
});

////////// method to delete a notice//////////
/**
 * url     -    /deletenotice   
 * method  -    POST
 * params  -    id (id of the notice)  
 */

$app->post('/deletenotice', function() use ($app){

    $id = $app->request->post('id');

    $notice = Notice::find($id);

    $notice->delete();

    $confirmation = Notice::find($id);

    if(!$confirmation){
            $responce['error'] = false;
            $responce['data'] = 'Success';
    } else{
            $responce['error'] = true;
            $responce['data'] = 'Error';
    }
    echoResponce($responce);
});


/*---------------------------------*/
/*---------------------------------*/
/*    methods on Requests          */
/*---------------------------------*/
/*---------------------------------*/

////////// method to get a request to join a project //////////
/**
 * url    - /request
 * method - POST
 * params - user_id (fbid of the user) , project_id (id of the project wanting to join)
 */
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!ALERT!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!//
// --Requires middleware to check the number of requests on the project (reject if above limit)   //                      
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!//

$app->post('/request', 'checkuser', 'checkproject',  function() use ($app){

        $user_id = $app->request->post('user_id');
        $project_id = $app->request->post('project_id');

        $request = Request::create([
                'user_id'       =>      $user_id,
                'project_id'    =>      $project_id
            ]);
    
        if($request->exists){
            $responce['error'] = false;
            $responce['data'] = 'Success';
        } else{
            $responce['error'] = true;
            $responce['data'] = 'Error';
        } 
        echoResponce($responce);

        $id = Project::find($project_id)->user_id;
        $ids = [User::find($id)->reg_id];
        $data = [
            'title' => 'One new project Request',
            'message' => User::find($user_id)->name . ' has requested to join your project ' . Project::find($project_id)->title,
            'tags' => 'project',
            'link' => "brainants://bsccsit/eachprojectadmin?project_id=".$project_id
        ];
        sendGCM($data, $ids);
 
});


////////// method to cancel a request //////////
/**
 * url    - /cancelrequest
 * method - POST
 * params - user_id, project_id
 */

$app->post('/cancelrequest', function() use ($app){

    $user_id = $app->request->post('user_id');
    $project_id = $app->request->post('project_id');

    $request = Request::where('user_id', '=', $user_id)->where('project_id', '=', $project_id);

    User::find($user_id)->projects()->detach($project_id);
    $request->delete();
    if(!$request->exists){
            $responce['error'] = false;
            $responce['data'] = 'Success';
    } else {
            $responce['error'] = true;
            $responce['data'] = 'Error';
    }
    echoResponce($responce);
    
});

/*---------------------------------*/
/*---------------------------------*/
/*    methods on notification      */
/*---------------------------------*/
/*---------------------------------*/

////////// method to get all notifications //////////
/**
 * url    - /allnotifications
 * method - GET
 * params - 
 */

$app->get('/allnotifications', function() use ($app){

    $notifications = Notification::all()->sortBy('updated_at');
    echoResponce($notifications);

});

/*---------------------------------*/
/*---------------------------------*/
/*        methods on tags          */
/*---------------------------------*/
/*---------------------------------*/

//////////method to get all tags//////////
/**
 * url    - /alltags
 * method - GET
 * params - 
 */

$app->get('/alltags', function() use($app) {

    $tags = Tag::all();
    echoResponce($tags);

});

////////// method to add a tag//////////
/**
 * url     -    /addtag  
 * method  -    POST
 * params  -    name (name of tag)
 */


$app->post('/addtag', function() use ($app){

    $name           =       $app->request->post('name');

    $tag = Tag::create([
        'name'            =>      $name
    ]);

    if($tag->exists){
            $responce['error'] = false;
            $responce['data'] = 'Success';
    } else {
            $responce['error'] = true;
            $responce['data'] = 'Error';
    }
    echoResponce($responce);
});

//////////method to delete a tag//////////
/**
 * url    - /deletetag
 * method - POST
 * params - id(tag id)
 */

$app->post('/deletetag', function() use ($app) {
    $id = $app->request->post('id');

    $tag = Tag::find($id);
    $tag->delete();

    $confirmation = Tag::find($id);

    if(!$confirmation){
            $responce['error'] = false;
            $responce['data'] = 'Success';
    } else{
            $responce['error'] = true;
            $responce['data'] = 'Error';
    }
    echoResponce($responce);

});



/*---------------------------------*/
/*---------------------------------*/
/*        Hybrid methods           */
/*---------------------------------*/
/*---------------------------------*/

////////// method to get project details//////////
/**
 * url    - /getproject
 * method - POST
 * params - project_id (id of the project)
 */

$app->post('/getproject','checkproject', function() use ($app){

    $id = $app->request->post('project_id');

    $project = Project::find($id);
    $members = Project::find($id)->users()->get();
    $user_id = $project->user_id;
    $admin = User::find($user_id);

    
    $project_tags = returnwithtags($project, $id);
    $requests = $project->requests()->get();
    $project_tags['requests'] = $requests;
    foreach ($requests as $request) {
        $user = User::find($request->user_id);
        $request['name'] = $user->name;
    }
    $project_tags['admin'] = $admin;
    $project_tags['members'] = $members;
    $responce['error'] = false;
    $responce['data'] = $project_tags;
    echoResponce($responce);    

});

////////// method to get a request on a users own project //////////
/**
 * url    - /getrequests
 * method - POST
 * params - user_id (fbid of the owner of the project)
 */

$app->post('/getrequests', 'checkuser', function() use ($app){

    $id = $app->request->post('user_id');

    $instance = array();
    $result = array();
    $projects = User::find($id)->ownProjects()->get();

    foreach($projects as $project){
        $request = Project::find($project->id)->requests()->get();
        $instance['id'] = $project->id;
        $instance['data'] = $request;
        array_push($result, $instance);
    }
            $responce['error'] = false;
            $responce['data'] = $result;
    echoResponce($responce);

});

////////// method to get requests on a project //////////
/** 
 * url    - /getprojectrequests
 * method - POST
 * params - project_id
 */

$app->post('/getprojectrequests', 'checkproject', function() use($app){

    $id = $app->request->post('project_id');
    $project = Project::find($id);
    $requests = $project->requests()->get();
    $responce['error'] = false;
    $responce['data'] = $requests;
    echoResponce($responce);
});

////////// method to accept a request on a project //////////
/**
 * url    - /accept
 * method - POST
 * params - id (id of the request)
 */

$app->post('/accept', 'checkrequest', 'checkuserinproject' , 'checkuserlimit', function() use ($app){
        
        $id = $app->request->post('id');

        $request_data = Request::find($id);
        $accept = User::find($request_data->user_id)->projects()->attach($request_data->project_id);
        $request_data->delete();

        increaseusernumber($request_data->project_id);


        $confirmation = User::find($request_data->user_id)->projects()->find($request_data->project_id);
        if($confirmation->pivot){
            $responce['error'] = false;
            $responce['data'] = 'Success';
        } else {
            $responce['error'] = true;
            $responce['data'] = 'Error';
        }
        echoResponce($responce);
        $ids = [User::find($request_data->user_id)->reg_id];
        $data = [
            'title' => 'Request Accepted',
            'message' => 'Your request on the project' . Project::find($request_data->project_id)->title . 'has been accepted',
            'tags' => 'project',
            'link' => 'brainants://bsccsit/eachproject?project_id='. Project::find($request_data->project_id)->id
        ];
        sendGCM($data, $ids);
        

});

////////// method to reject a request //////////
/**
 * url    - /cancelrequest
 * method - POST
 * params - id(id of request)
 */
$app->post('/reject', function() use ($app){
    
    $id = $app->request->post('id');

    $request_data = Request::find($id);
    $reject = User::find($request_data->user_id)->projects()->detach($request_data->project_id);
    $request_data->delete();
    if(!$confirmation->pivot){
            $responce['error'] = false;
            $responce['data'] = 'Success';
        } else {
            $responce['error'] = true;
            $responce['data'] = 'Error';
        }
    $ids = [User::find($request_data->user_id)->reg_id];
    $data = [
        'title' => 'Request Delined',
        'message' => 'Your request on the project ' . Project::find($request_data->project_id)->title . 'has been rejected by the admin',
        'tags' => 'project',
        'link' => 'brainants://bsccsit/eachproject?project_id='. $request_data->project_id
    ];
    sendGCM($data, $ids);
});

//////////method to get the users on a project//////////
/**
 * url    - /getprojectusers
 * method - POST
 * params - project_id
 */

$app->post('/getprojectusers', function() use($app){

    $id = $app->request->post('project_id');
    $project = Project::find($id);
    $members = Project::find($id)->users()->get();
    $user_id = $project->user_id;
    $admin = User::find($user_id);

    $result['admin'] = $admin;
    $result['members'] = $members;

    $responce['error'] = false;
    $responce['data'] = $result;
    echoResponce($responce);

});

//////////method to get all projects of a tag //////////
/** 
 * url    - /tagprojects
 * method - POST
 * params - id
 */

$app->post('/tagprojects', function() use ($app){

    $tag = $app->request->post('tag');

    $tag = Tag::where('name', '=', $tag)->first();
    $project_tags = [];
    $projects = $tag->projects()->get();
    foreach ($projects as $project) {
        $project_tags[] = returnwithtags($project, $project->id);
    }
    $responce['error'] = false;
    $responce['data'] = $project_tags;
    echoResponce($responce);
    
});

/*---------------------------------*/
/*---------------------------------*/
/*               GCM               */
/*---------------------------------*/
/*---------------------------------*/

  //////////method to send gcm to one person //////////
/** 
 * url    - /sendtoone
 * method - POST
 * params - user_id, title, message , link , tags
 */

$app->post('/sendtoone', function () use ($app) {

    $user_id = $app->request->post('user_id');
    $title = $app->request->post('title');
    $message = $app->request->post('message');
    $link = $app->request->post('link');
    $tags = $app->request->post('tag');

    $data = [
        'title' => $title,
        'message' => $message,
        'tag' => $tags,
        'link' => $link
    ];
    echoResponce($data);
    //$ids = [User::find($user_id)->reg_id];
    //sendGCM($data, $ids);
});
//////////method to send gcm to all users//////////
/** 
 * url    - /sendtoall
 * method - POST
 * params - title, message , link , tags
 */
$app->post('/sendtoall', function () use ($app) {

    $title = $app->request->post('title');
    $message = $app->request->post('message');
    $link = $app->request->post('link');
    $tags = $app->request->post('tag');

    $data = [
        'title' => $title,
        'message' => $message,
        'link' => $link,
        'tag' => $tags
    ];
    $ids = [];
    $users = User::all();
    foreach ($users as $user) {
        $ids[] = $user->reg_id;
    }
    sendGCM($data, $ids);
});
//////////method to send gcm to users of a semester//////////
/** 
 * url    - /sendtoallbysemester
 * method - POST
 * params - semester, title, message , link , tags
 */
    
$app->post('/sendtoallbysemester', function() use($app){

    $title = $app->request->post('title');
    $message = $app->request->post('message');
    $link = $app->request->post('link');
    $tags = $app->request->post('tag');
    $semester = $app->request->post('semester');

    $data = [
        'title' => $title,
        'message' => $message,
        'link' => $link,
        'tag' => $tags
    ];
    $ids = [];
    $users = User::where('semester', '=', $semester);
    foreach ($users as $user) {
        $ids[] = $user->reg_id;
    }
    sendGCM($data, $ids);
});

//////////method to send gcm to users of a college //////////
/** 
 * url    - /sendtoallbycollege
 * method - POST
 * params - college, title, message , link , tags
 */
$app->post('/sendtoallbycollege', function() use ($app){

    $title = $app->request->post('title');
    $message = $app->request->post('message');
    $link = $app->request->post('link');
    $tags = $app->request->post('tag');
    $college = $app->request->post('college');
    $data = [
        'title' => $title,
        'message' => $message,
        'link' => $link,
        'tag' => $tags
    ];
    $ids = [];
    $users = User::where('college', '=', $college);
    foreach ($users as $user) {
        $ids[] = $user->reg_id;
    }
    sendGCM($data, $ids);
});

//////////method to send gcm to users of a college and a semester //////////
/** 
 * url    - /sendtoallbycollegesemester
 * method - POST
 * params - id
 */

$app->post('/sendtoallbycollegesemester', function() use($app){

    $title = $app->request->post('title');
    $message = $app->request->post('message');
    $link = $app->request->post('link');
    $tags = $app->request->post('tag');
    $college = $app->request->post('college');
    $semester = $app->request->post('semester');
    $data = [
        'title' => $title,
        'message' => $message,
        'link' => $link,
        'tag' => $tags
    ];
    $ids = [];
    $users = User::where('college', '=', $college)->where('semester', '=', $semester);
    foreach ($users as $user) {
        $ids[] = $user->reg_id;
    }
    sendGCM($data, $ids);
});

//////////method to send gcm to users of a project //////////
/** 
 * url    - /sendtoallbyproject
 * method - POST
 * params - id
 */


$app->post('/sendtoallbyproject', function() use($app){

    $title = $app->request->post('title');
    $message = $app->request->post('message');
    $link = $app->request->post('link');
    $tags = $app->request->post('tag');
    $project_id = $app->request->post('project_id');
    $data = [
        'title' => $title,
        'message' => $message,
        'link' => $link,
        'tag' => $tags
    ];
    $ids = [];
    $users = Project::find($project_id)->users();
    foreach ($users as $user) {
        $ids[] = $user->reg_id;
    }
    sendGCM($data, $ids);
});

/*---------------------------------*/
/*---------------------------------*/
/*      Methods on Survey          */
/*---------------------------------*/
/*---------------------------------*/

//////////All surveys//////////
/** 
 * url    - /allsurveys
 * method - GET
 * params -
 */
$app->get('/allsurveys', function( ) use ($app){

    $data = Survey::all();
    echoResponce($data);

});

























  //////////DUMMY//////////
/** 
 * url    - /dummy
 * method - POST
 * params - dummy1, dummy2
 */

$app->post('/dummy', function () use ($app) {

    $dummy1 = $app->request->post('dummy1');
    $dummy2 = $app->request->post('dummy2');
    
    $data = [
        'dummy1' => $dummy1,
        'dummy2' => $dummy2
    ];
    echoResponce($data);
});
/*---------------------------------*/
/*---------------------------------*/
/*    functions and middlewares    */
/*---------------------------------*/
/*---------------------------------*/

function echoResponce($data)
{

    $app = \Slim\Slim::getInstance();
    $app->contentType('application/json');
    echo json_encode($data);

}

function checkuser($id)
{
    $app = \Slim\Slim::getInstance();
    $id = $app->request->params('user_id');
    $user = User::find($id);
    if(!$user){
        $responce['error'] = true;
        $responce['data'] = 'Error, user doesnot exist';
        echoResponce($responce);
        $app->stop();
    }
}

function checkcommunity($id)
{
    $app = \Slim\Slim::getInstance();
    $id = $app->request->params('comm_id');
    $community = Community::find($id);
    if(!$community){
        $responce['error'] = true;
        $responce['data'] = 'Error, Community doesnot exist';
        echoResponce($responce);
        $app->stop();
    }
}

function checkproject($id)
{
    $app = \Slim\Slim::getInstance();
    $id = $app->request->params('project_id');
    $project = Project::find($id);
    if(!$project){
        $responce['error'] = true;
        $responce['data'] = 'Error, project doesnot exist';
        echoResponce($responce);
        $app->stop();
    }
}

function checkrequest($id)
{
    $app = \Slim\Slim::getInstance();
    $id = $app->request->params('id');
    $request = Request::find($id);
    if(!$request){
        $responce['error'] = true;
        $responce['data'] = 'Error, Request doesnot exist';
        echoResponce($responce);
        $app->stop();
    }
}

function checkuserinproject($id)
{
    $app = \Slim\Slim::getInstance();
    $id = $app->request->params('id');
    $request = Request::find($id);
    $project_id = $request->project_id;
    $user_id = $request->user_id;
    $project = Project::find($project_id)->users()->find($user_id);
    if($project){
        $responce['error'] = true;
        $responce['data'] = 'Error, user already exists in project';
        echoResponce($responce);
        $app->stop();
    }
}

function attachtags($id)
{
    $app = \Slim\Slim::getInstance();
    $comma_seperated_tags = $app->request->params('tags');

    $tags = explode(",", $comma_seperated_tags);
    foreach($tags as $tag){
    $temp = Tag::where('name', '=', $tag)->first();
    $tagid = $temp->id;
        Project::find($id)->tags()->attach($tagid);
    }
}

function increaseusernumber($id)
{
    $app = \Slim\Slim::getInstance();
    $project = Project::find($id);
    $project->num_users++;
    $project->save();
}

function checkuserlimit($id)
{
    $app = \Slim\Slim::getInstance();
    $id = $app->request->params('id');

    $request = Request::find($id);
    $project_id = $request->project_id;
    $project = Project::find($project_id);
    if($project->num_users >= $project->required_users){
        $responce['error'] = true;
        $responce['data'] = 'Error, user limit exceeded';
        echoResponce($responce);
        $app->stop();
    }
}


function returnwithtags($data, $id)
{
    $project = Project::find($id);
    $taglist['tags'] = [];
    $tags = $project->tags()->get();
    foreach ($tags as $tag) {
        array_push($taglist['tags'], $tag->name);
    }
    $comma_seperated_tags = implode(',', $taglist['tags']);
    $taglist['tags'] = $comma_seperated_tags;
    $data['tags'] = $taglist['tags'];
    return($data);
}

function returnwithmembers($data, $id)
{
    $project = Project::find($id);
    $memberlist['members'] = [];
    $members = $project->users()->get();
    foreach ($members as $member) {
        array_push($memberlist['members'], $member->id);
    }
    $comma_seperated_members = implode(',', $memberlist['members']);
    $memberlist['members'] = $comma_seperated_members;
    $data['members'] = $memberlist['members'];
    return($data);
}

function sendGCM( $data, $ids )
{
    // Insert real GCM API key from Google APIs Console
    // https://code.google.com/apis/console/        
    $apiKey = 'AIzaSyC0Ju7wsk6tqCiMLx7Ox-UddvO2qGRKl7w';

    // Define URL to GCM endpoint
    $url = 'https://android.googleapis.com/gcm/send';

    // Set GCM post variables (device IDs and push payload)     
    $post = array(
                    'registration_ids'  => $ids,
                    'data'              => $data,
                    );

    // Set CURL request headers (authentication and type)       
    $headers = array( 
                        'Authorization: key=' . $apiKey,
                        'Content-Type: application/json'
                    );

    // Initialize curl handle       
    $ch = curl_init();

    // Set URL to GCM endpoint      
    curl_setopt( $ch, CURLOPT_URL, $url );

    // Set request method to POST       
    curl_setopt( $ch, CURLOPT_POST, true );

    // Set our custom headers       
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

    // Get the response back as string instead of printing it       
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

    // Set JSON post data
    curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $post ) );

    // Actually send the push   
    $result = curl_exec( $ch );

    // Error handling
     if ( curl_errno( $ch ) )
     {
         echo 'GCM error: ' . curl_error( $ch );
    }

    // Close curl handle
    curl_close( $ch );

    // Debug GCM response       
    echo $result;


}





$app->run();
