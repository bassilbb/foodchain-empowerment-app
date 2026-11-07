<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamMemberController extends Controller
{
    public function index()
    {
        $members = TeamMember::ordered()->get();
        return view('admin.team-members.index', compact('members'));
    }

    public function create()
    {
        $member = new TeamMember(['is_active' => true, 'sort_order' => (TeamMember::max('sort_order') ?? 0) + 10]);
        return view('admin.team-members.form', compact('member'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('team-members', 'public');
        }
        $member = TeamMember::create($data);
        return redirect()->route('admin.team-members.edit', $member)->with('success', "\"{$member->name}\" has been added.");
    }

    public function edit(TeamMember $team_member)
    {
        $member = $team_member;
        return view('admin.team-members.form', compact('member'));
    }

    public function update(Request $request, TeamMember $team_member)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        if ($request->hasFile('photo')) {
            $this->deletePhotoIfOwned($team_member);
            $data['photo'] = $request->file('photo')->store('team-members', 'public');
        } elseif ($request->boolean('remove_photo')) {
            $this->deletePhotoIfOwned($team_member);
            $data['photo'] = null;
        }
        $team_member->update($data);
        return redirect()->route('admin.team-members.edit', $team_member)->with('success', "\"{$team_member->name}\" has been updated.");
    }

    public function toggle(TeamMember $team_member)
    {
        $team_member->update(['is_active' => ! $team_member->is_active]);
        $status = $team_member->is_active ? 'enabled' : 'disabled';
        return redirect()->route('admin.team-members.index')->with('success', "\"{$team_member->name}\" {$status}.");
    }

    public function destroy(TeamMember $team_member)
    {
        $name = $team_member->name;
        $this->deletePhotoIfOwned($team_member);
        $team_member->delete();
        return redirect()->route('admin.team-members.index')->with('success', "\"{$name}\" has been deleted.");
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp,avif', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);
        return [
            'name' => $validated['name'],
            'role' => $validated['role'],
            'bio' => $validated['bio'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ];
    }

    private function deletePhotoIfOwned(TeamMember $member): void
    {
        if ($member->photo && ! str_starts_with($member->photo, 'assets/') && Storage::disk('public')->exists($member->photo)) {
            Storage::disk('public')->delete($member->photo);
        }
    }
}
