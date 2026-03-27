<?php

namespace App\Http\Controllers;

use App\Models\Yearbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class YearbookController extends Controller
{
    public function index(Request $request)
    {
        $query = Yearbook::query();

        if ($search = trim((string) $request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%");
            });
        }

        $yearbooks = $query->orderBy('year', 'desc')->orderBy('title')->get();

        $groupedByDecade = $yearbooks
            ->groupBy(function (Yearbook $yearbook) {
                $start = (int) (floor($yearbook->year / 10) * 10);
                return sprintf('%d-%d', $start, $start + 9);
            })
            ->sortKeysDesc();

        return view('yearbook.index', [
            'groupedByDecade' => $groupedByDecade,
            'search' => $search,
        ]);
    }

    public function manage(Request $request)
    {
        $query = Yearbook::query();

        if ($search = trim((string) $request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%");
            });
        }

        $yearbooks = $query
            ->orderBy('year', 'desc')
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        return view('yearbook.manage', [
            'yearbooks' => $yearbooks,
        ]);
    }

    public function create()
    {
        return view('yearbook.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages());

        $validated['pdf_file'] = $request->file('pdf_file')->store('yearbooks', 'public');

        Yearbook::create($validated);

        return redirect()->route('yearbook.manage')->with('status', 'Yearbook uploaded successfully.');
    }

    public function edit(Yearbook $yearbook)
    {
        return view('yearbook.edit', [
            'yearbook' => $yearbook,
        ]);
    }

    public function update(Request $request, Yearbook $yearbook)
    {
        $validated = $request->validate(
            $this->rules($yearbook->id, false),
            $this->messages()
        );

        if ($request->hasFile('pdf_file')) {
            if ($yearbook->pdf_file && Storage::disk('public')->exists($yearbook->pdf_file)) {
                Storage::disk('public')->delete($yearbook->pdf_file);
            }
            $validated['pdf_file'] = $request->file('pdf_file')->store('yearbooks', 'public');
        }

        $yearbook->update($validated);

        return redirect()->route('yearbook.manage')->with('status', 'Yearbook updated successfully.');
    }

    public function destroy(Yearbook $yearbook)
    {
        if ($yearbook->pdf_file && Storage::disk('public')->exists($yearbook->pdf_file)) {
            Storage::disk('public')->delete($yearbook->pdf_file);
        }

        $yearbook->delete();

        return redirect()->route('yearbook.manage')->with('status', 'Yearbook deleted successfully.');
    }

    private function rules(?int $yearbookId = null, bool $pdfRequired = true): array
    {
        $maxYear = now()->year + 1;

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('yearbooks', 'title')->where(function ($query) {
                    return $query->where('year', request()->input('year'));
                })->ignore($yearbookId),
            ],
            'year' => ['required', 'integer', 'min:1900', "max:{$maxYear}"],
            'pdf_file' => [
                $pdfRequired ? 'required' : 'nullable',
                'file',
                'mimes:pdf',
                'max:20480',
            ],
        ];
    }

    private function messages(): array
    {
        return [
            'title.required' => 'The yearbook title is required.',
            'title.max' => 'The title must not exceed 255 characters.',
            'title.unique' => 'A yearbook with this title and year already exists.',
            'year.required' => 'The publication year is required.',
            'year.integer' => 'The year must be a valid number.',
            'year.min' => 'The year must be 1900 or later.',
            'year.max' => 'The year cannot be in the far future.',
            'pdf_file.required' => 'Please upload a PDF file.',
            'pdf_file.file' => 'The uploaded yearbook must be a valid file.',
            'pdf_file.mimes' => 'Only PDF files are allowed.',
            'pdf_file.max' => 'The PDF file must not exceed 20MB.',
        ];
    }
}
