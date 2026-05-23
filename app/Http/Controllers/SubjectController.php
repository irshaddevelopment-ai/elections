<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
class SubjectController extends Controller
{
    //=====================subjects
    public function ShowSubjectManagerForm(Request $request)
    {
        $full_name = session('full_name', '');
        if ($full_name != '') {
            return view('subjectmanager', ['full_name' => $full_name, 'Profiles' => '']);
        } else {
            return view('welcome');
        }
    }

    public function ShowSubjectsListForm(Request $request)
    {
        $full_name = session('full_name', '');
        if ($full_name != '') {
            $Subjects = Subject::all();
            return view('subjectslist', ['full_name' => $full_name, 'Subjects' => $Subjects]);
        } else {
            return view('welcome');
        }
    }

    public function saveSubjectInfo(Request $request){
        try {
            DB::unprepared('LOCK TABLES subjects WRITE');
            DB::beginTransaction();
            // Lock the table for writing
            //================save profile
            $subjec_var = new Subject();
            $subjec_var->title = $request->input('input_subjecttitle');
            $subjec_var->description = $request->input('input_desc');
           

            $subject_code = $request->input('input_subject_code');
        
            $image_file = $request->file('subject_picture');
            $subjec_var->picture = "";

            if ($subject_code == "") {
                $max_subject_Id = Subject::max('idsubjects') + 1;
                $subjec_var->subject_code = 'sub_' . $max_subject_Id;

                if (isset($image_file)) {
                    $imageName = $subjec_var->subject_code . '.' . $image_file->getClientOriginalExtension();
                    $subjec_var->picture = $imageName;
                }
                $subjec_var->save();
            } else {
                if (isset($image_file)) {
                    $imageName = $subject_code . '.' . $image_file->getClientOriginalExtension();
                    $subjec_var->picture = $imageName;
                }
                Subject::where('profile_code', $subject_code)
                    ->update([
                        'title' => $subjec_var->title,
                        'description' => $subjec_var->description,
                        'picture' => $subjec_var->picture
                    ]);
            }
            if (isset($image_file)) {
                $fileName = $image_file->getClientOriginalName();
                $subjec_var->picture = $fileName;

                // Validate the file if needed
                $request->validate([
                    'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
                // Specify the destination folder
                $destinationPath = public_path('profile_picture');
                // Check if the file with the same name already exists
                $existingFile = $destinationPath . '/' . $imageName;

                if (File::exists($existingFile)) {
                    // If it exists, delete the existing file
                    File::delete($existingFile);
                }

                // Move the uploaded file to the destination folder
                $image_file->move($destinationPath, $imageName);
            }
            
            DB::commit();
            DB::unprepared('UNLOCK TABLES');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return back()->with('Success', 'Save success');
    }
}
