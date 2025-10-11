<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class LiRAController extends Controller
{
    public function showForm(Request $request)
    {
        $user = Auth::user();
        $sf = $user->studentFaculty ?? null;
        $first = $sf->first_name ?? '';
        $middle = $sf->middle_name ?? '';
        $last = $sf->last_name ?? '';
        $email = $user->email ?? '';
        $department = $sf->department ?? '';
        $course = $sf->course ?? '';
        $yrlvl = $sf->yrlvl ?? '';
        $programStrandGradeLevel = trim($course . ($yrlvl ? '-' . $yrlvl : '')) ?: 'BSSW-4';
        $designationRaw = $sf->role ?? 'Faculty';
        $designation = ucfirst(strtolower($designationRaw));

        // Get catalog info from query parameters
        $title = $request->query('title', '');
        $author = $request->query('author', '');
        $call_number = $request->query('call_number', '');
        $isbn = $request->query('isbn', '');
        $lccn = $request->query('lccn', '');
        $issn = $request->query('issn', '');

        // Compose examplePurposive: Title, Author, Call number, LCCN/ISBN/ISSN
        $examplePurposive = '';
        $examplePurposive .= $title ? $title . ', ' : '';
        $examplePurposive .= $author ? $author . ', ' : '';
        $examplePurposive .= $call_number ? $call_number . ', ' : '';

        $idParts = [];
        if ($lccn) $idParts[] = "LCCN: $lccn";
        if ($isbn) $idParts[] = "ISBN: $isbn";
        if ($issn) $idParts[] = "ISSN: $issn";

        if (!empty($idParts)) {
            $examplePurposive .= implode(', ', $idParts);
        } else {
            $examplePurposive = rtrim($examplePurposive, ', ');
        }

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

        $jotformUrl = $baseUrl . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        return view('lira.jotform', compact('jotformUrl'));
    }
}
