<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\DB;   
use App\Models\Person;
use App\Models\State;
class RegisterController extends BaseController
{    
    /**
     * Function name : login
     * Purpose : It login user and return access token
     */
    public function login(Request $request)
    {      
        try
        {     
            $user_data=User::select("*")->where(array("email"=>$request->email,"pass"=>md5($request->password)))->first();                   
            if($user_data)
            { 
                $user = $user_data;                             
                $success['token'] =  $user->createToken('MyApp')->plainTextToken;            
                return $this->sendResponse($success, 'User login successfully.');
            } 
            else
            { 
                return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
            } 
        } 
        catch (\Exception $e) 
        {
            return $this->sendError($e->getMessage());
        }      
    }
    /**
     * Function name : logout
     * Purpose : It logout user
     */
    public function logout(Request $request)
    {        
        try
        {
            $request->user()->currentAccessToken()->delete();
            return $this->sendResponse('', 'You are logged out successfully.');
        
        } 
        catch (\Exception $e) 
        {
            return $this->sendError($e->getMessage());
        }     
    } 

    /**
     * Function name : user_detail
     * Purpose : it returns details of currrent user     
    */
    public function error_page()
    {
        return $this->sendError('Invalid token.', []);        
    }
     public function user_detail(Request $request)
    {   
        try
        {          
            $person_id=$request->user()->person_id;
            $result=array();
    
            $user_data = DB::table('login as l')
                ->join('person as p', 'p.id', '=', 'l.person_id')        
                ->select('p.name','l.email','l.active_from','l.active_thru','l.is_primary' )
                ->where("l.person_id","=",$person_id)
                ->first();           
            
            if($user_data)
            {
                $user_data=obj_to_array($user_data);            
                $result=$user_data;
                $result['address_details']=array();
                
                $address_data = DB::table('person as p')
                        ->join('person_address as pa', 'pa.person_id', '=', 'p.id')
                        ->join('address as a', 'a.id', '=', 'pa.address_id')
                        ->where('p.id', $person_id)
                        ->get()->toArray();
                if($address_data)
                {             
                    foreach($address_data  as $key=>$value)
                    {    
                        $address_info=obj_to_array($value);
                        $state_country=State::with('country')->where('id',$address_info['state_id'])->first()->toArray();                    
                        if($state_country)
                        { 
                             $address_info['state']=$state_country['name'];                         
                             $address_info['country']=$state_country['country']['name'];                         
                             $result['address_details'][]=$address_info;
                        }
                    }
                }
                return $this->sendResponse($result, 'User details found.');  
            }
            else
            {
                return $this->sendError('Invalid user id.', ['error'=>'Please enter valid user id.']);
            }   
        }
        catch (\Exception $e) 
        {
            return $this->sendError($e->getMessage());
        }  
    }    
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    /*
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = md5($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
    */
}