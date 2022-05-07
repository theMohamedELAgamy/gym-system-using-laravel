<?php
namespace App\Http\Controllers;

use Error;
use Illuminate\Http\Request;
use Session;
use Stripe;
use App\Models\PurchaseOperation;
use App\Models\TrainingPackage;
use App\Models\Gym;
use App\Models\GymManger;
use App\Models\Trainee;
use Illuminate\Support\Facades\Auth;

class StripeController extends Controller
{
    public function handleGet2()
    {
        $packages = [];
        foreach (TrainingPackage::all() as $k) {
            array_push($packages, [$k["id"] , $k["name"] , $k["price"],$k["num_of_sessions"]]);
        }

        $gym_man = GymManger::where('user_id', Auth::id())->first();
        $gyms = [];
        $gymFlag = "YES";
        if ($gym_man !== null) {
            $my_gym = Gym::where("id", "=", $gym_man->gym_id)->first();
            
            $gyms =  array($my_gym->id, $my_gym->name, $my_gym->city_name) ;
        // dd($gyms);
        } else {
            foreach (Gym::all() as $k) {
                array_push($gyms, [$k["id"] , $k["name"], $k["city_name"]]);
            }
            $gymFlag ="NO";
        }
        
        return view('purchase_operations.payment2', ['packages' => $packages, 'gyms' => $gyms, 'gym_flag' => $gymFlag]);
    }
  
    public function handlePost2(Request $request)
    {
        \Stripe\Stripe::setApiKey('sk_test_51KvM1uKyzqgHTfADBaOGi0FtjuZ9vq9OH8e7paXnv2cyswX3u2XmbnMgjiKVxxBkmx15hgqFyb80eSFxc7mEohSr00DjK2ePO4');
        
        $input = $request->all();
        $trn = Trainee::where('email', '=', $input["trainee_email"])->first();
        $packagePrice = TrainingPackage::find($input["package_id"])->price;
        
        if ($trn === null) {
            Session::flash('fail', 'Invalid Trainee Data');
            return back();
        } else {
            $amount = intval($packagePrice) * 100;
            // dd($amount);
            try {
                \Stripe\Charge::create([
                    "amount" => $amount,
                    "currency" => "usd",
                    "source" => $request->stripeToken,
                    "description" => "Making test payment."
            ]);
                // Payment Successful - Try making a record;
                try {
                    $operation= PurchaseOperation::create([
                        'trainee_id'=>$trn->id,
                        'package_id'=>$input['package_id'],
                        'gym_id'=>$input['gym_id'],
                        'created_by_id'=>Auth::id(),
                        'price'=>$packagePrice,
                      ])->id;
                    Session::flash('success', 'Payment has been successfully processed.');
                    $trn->remaining_session += TrainingPackage::find($input["package_id"])->num_of_sessions;
                    $trn->save();

                    $trn_email = $trn->email;
                    $trn_sessions = $trn -> remaining_session;
                    return redirect()->route('purchase_operations')->with('success', "Payment was successful for $trn_email - Remaining Sessions:( $trn_sessions)") ;
                } catch (\Throwable $th) {
                    error_log($th);
                    Session::flash('fail', 'Fatal DB Error - Invalid Trainee Data');
                    return back();
                }
            } catch (\Throwable $th) {
                dd($th);
                Session::flash('fail', 'Payment Failed!');
                return back();
            }
        }
    }
}