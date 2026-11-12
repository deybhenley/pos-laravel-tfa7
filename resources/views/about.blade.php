@extends('layouts.app')
@section('title', 'About')
@section('content')
    <div class="page-heading"><p class="eyebrow">ABOUT THE PROJECT</p><h1>POS Foundations</h1><p>A customer and user account application for IT0049 Technical Formative Assessment 7.</p></div>
    <section class="panel prose">
        <h2>Account management</h2><p>The application lets staff create and edit customer contact information and user account records. Forms display clear validation messages and retain previously entered text when a submission needs correction.</p>
        <h2>Profile pictures</h2><p>Existing user accounts can receive a JPEG, PNG, or WebP profile picture up to 2 MB. Uploaded pictures are prepared as square thumbnails for the team directory. Accounts without a picture use a placeholder.</p>
        <h2>Demonstration scope</h2><p>The initial records are fictional. This coursework stage provides account listings, creation, editing, and avatar upload. It does not implement sign-in, sales processing, or account deletion.</p>
    </section>
@endsection
