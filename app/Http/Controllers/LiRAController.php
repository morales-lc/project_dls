<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class LiRAController extends Controller
{


    public function showForm(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $first = $user->studentFaculty->first_name ?? '';
        $middle = $user->studentFaculty->middle_name ?? '';
        $last = $user->studentFaculty->last_name ?? '';
        $email = $user->email ?? '';
        $department = $user->studentFaculty->department ?? 'Senior High';
        $course = $user->studentFaculty->course ?? '';
        $yrlvl = $user->studentFaculty->yrlvl ?? '';
        $programStrandGradeLevel = trim($course . ($yrlvl ? '-' . $yrlvl : '')) ?: 'BSSW-4';
        $designationRaw = $user->studentFaculty->role ?? 'Faculty';
        $designation = ucfirst(strtolower($designationRaw));

        $examplePurposive = $request->input('examplePurposive') ?? 'Purposive communication, Zoleta, Ma. Antonieta G., 302.2 Z74 2018 c1';
        // Decode to avoid + signs in display
        $examplePurposive = urldecode($examplePurposive);

        $baseUrl = 'https://jotform.com/221923899504465';
        $params = [
            'name[first]' => $first,
            'name[middle]' => $middle,
            'name[last]' => $last,
            'email11' => $email,
            'department' => $department,
            'programstrandgradeLevel' => $programStrandGradeLevel,
            'designation' => $designation,
            'whatKind' => 'Book Borrowing',
            'whatType' => 'Books',
            'examplePurposive' => $examplePurposive,
            'forList' => '',
            'forVideos' => '',
            'typeA43' => 'Yes',
            'titlesOf' => '',
        ];
        // Use http_build_query with PHP_QUERY_RFC3986 to avoid + for spaces
        $jotformUrl = $baseUrl . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        return view('lira.jotform', compact('jotformUrl'));
    }
}
