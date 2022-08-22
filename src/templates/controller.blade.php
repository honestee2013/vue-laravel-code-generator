
namespace App\Http\Controllers\Honestee\VueCodeGen;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Schema;




//use App\Models\Honestee\VueCodeGen\ Replace this with the model name & uncomment it;
use App\Models\Honestee\VueCodeGen\{{$data['singular']}};
//use App\Models\User;
use DB;


class {{ $data['singular'] }}Controller extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return  void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Gate::allows('isAdmin')) {
            return $this->unauthorizedResponse();
        }
        // $this->authorize('isAdmin');
        $page = $request->query('page', 1);
        $perPage = $request->query('perPage', '5');
        $sortType = $request->query('sortType', 'asc');
        $sortField = $request->query('sortField');
        $searchTerm = $request->query('searchTerm', '');

        //$count = {{$data['singular']}}::all()->count();

        $query = {{$data['singular']}}::query();
        if($request['searchTerm'])
            $query = $this->search($request, $query);
        
        if($sortField)
            ${{$data['plural_lower']}} = $query->orderBy($sortField, $sortType)->paginate( $perPage );
        else       
            ${{$data['plural_lower']}} = $query->paginate( $perPage );

            //${{$data['plural_lower']}}->makeHidden(['id', 'created_at', 'updated_at']);
        


            /*$users = User::select("*")
            ->where('id', 23)
            ->orWhere('email', 'itsolutionstuff@gmail.com')
            ->get();*/    


        return $this->sendResponse(${{$data['plural_lower']}}, '{{$data['plural']}} list ');
    }


    public function search($request, $query)
    {
        /*if (!Gate::allows('isAdmin')) {
            return $this->unauthorizedResponse();
        }*/
        // $this->authorize('isAdmin');

        
        $fields = Schema::getColumnListing('${{$data['plural_lower']}}');
       
    
        //if($request['search']){
            foreach( $fields as $field) {
                $query = $query->orWhere($field, 'LIKE', '%'.$request['searchTerm']. '%');
            }
        return $query;
            //${{$data['plural_lower']}} = $query->paginate(10);
       // } else {
            //${{$data['plural_lower']}} = {{$data['singular']}}::latest()->paginate(10);
       // }
        //return $this->sendResponse(${{$data['plural_lower']}}, 'Search result');
    }


    public function ajaxtest(){
        return "API Response frrom controller";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param    \App\Http\Requests\${{$data['plural']}}\PostRequest  $request
     *
     * @param  $id
     *
     * @return  \Illuminate\Http\Response
     * @throws  \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->checkValidation($request);
        ${{$data['singular_lower']}} = {{$data['singular']}}::create($request->all());    
        return $this->sendResponse(${{$data['singular_lower']}}, '{{$data['singular']}} Created Successfully');
    }



    public function checkValidation(Request $request){
        $data = DB::select('DESCRIBE '.strtolower( '{{$data['plural']}}' ));
        
        $validationInfo = array();

        foreach($data as $column){  // First array element as  Require field definition 
            //extract the number for the max attribute
            preg_match_all('!\d+!', $column->Type, $matches);
            $max = (isset($matches[0][0])) ? (int)$matches[0][0] : false;
            $required = ($column->Null == 'NO') ? true : false ;
            if($required && $max && $column->Field != "id" && $column->Field !="created_at" && $column->Field !="updated_at" )
                $validationInfo[$column->Field] = 'required|max:'.$max;
            else if($required && $column->Field != "id" && $column->Field !="created_at" && $column->Field !="updated_at" )
                $validationInfo[$column->Field] = 'required';

        }

        foreach($data as $column){ // Second array element as  Require field error messages
            //extract the number for the max attribute
            preg_match_all('!\d+!', $column->Type, $matches);
            $max = (isset($matches[0][0])) ? (int)$matches[0][0] : false;

            // Extract if its required
            $required = ($column->Null == 'NO') ? true : false ;

            if($required && $column->Field != "id" && $column->Field !="created_at" && $column->Field !="updated_at" ){
                $validationInfo[$column->Field.'.required'] = $column->Field.' is a required field.';
            }

            if($max && $column->Field != "id" && $column->Field !="created_at" && $column->Field !="updated_at" ){
                $validationInfo[$column->Field.'.max'] = $column->Field.' can only be '.$max.' characters.';
            }
        }

        return $request->validate($validationInfo);

    }


    /**
     * Update the resource in storage
     *
     * @param    \App\Http\Requests\${{$data['plural']}}\PostRequest  $request
     * @param  $id
     *
     * @return  \Illuminate\Http\Response
     * @throws  \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {

        $this->checkValidation($request);
       
        ${{$data['singular_lower']}} = {{$data['singular']}}::findOrFail($id);
        $input = $request->all();
        ${{$data['singular_lower']}}->fill($input)->save();
                        
        /*return ${{$data['plural_lower']}};

        ${{$data['singular_lower']}} = {{$data['singular']}}::findOrFail($id);

        if (!empty($request->password)) {
            $request->merge(['password' => Hash::make($request['password'])]);
        }

        ${{$data['singular_lower']}}->update($request->all());*/

        return $this->sendResponse(${{$data['singular_lower']}}, '{{$data['singular']}} Information has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param    int  $id
     * @return  \Illuminate\Http\Response
     */
    public function destroy($idsStr)
    {
        $idsArray = json_decode($idsStr,true);
        {{$data['singular']}}::whereIn('id', $idsArray)->delete();
        return $this->sendResponse($idsStr, "The record was deleted successfully.");
    }



    /** 
    * success response method.
     *
     * @param  $result
     * @param  $message
     *
     * @return  \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'success'        => true,
            'message'        => $message,
            'data'           => $result,
        ];
        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @param  $error
     * @param    array  $errorMessages
     * @param    int  $code
     *
     * @return  \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];


        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }


        return response()->json($response, $code);
    }


    /**
     * return Unauthorized response.
     *
     * @param  $error
     * @param    int  $code
     *
     * @return  \Illuminate\Http\Response
     */
    public function unauthorizedResponse($error = 'Forbidden', $code = 403)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        return response()->json($response, $code);
    }



}