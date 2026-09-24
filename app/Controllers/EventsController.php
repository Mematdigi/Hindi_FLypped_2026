<?php

namespace App\Controllers;

use App\Models\EventModel;

class EventsController extends BaseController
{
    public function index()
    {
        $eventModel = new EventModel();
        
        // Fetch events from the database
        $data['events'] = $eventModel->findAll();

        // Fetch footer data using the fetchFooterData helper function
        $footer_data = $this->fetchFooterData();
    
        // Add footer data to the data array
        $data['latest_footer_blogs'] = $footer_data['latest_footer_blogs']; // Footer latest blogs
        $data['categories_with_post_count'] = $footer_data['categories_with_post_count']; // Footer category post counts

        // Load the events view
        return view('events/index', $data);
    }

    public function show($id)
{
    $eventModel = new EventModel();

    // Find the event by ID
    $data['event'] = $eventModel->find($id);

    // Check if the event exists
    if (!$data['event']) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Event not found");
    }

    // Check ticket availability and add it to the data array
    $totalTickets = $data['event']['total_tickets'];
    $data['ticket_status'] = $totalTickets > 0 ? ($totalTickets <= 5 ? 'Few Tickets Left' : 'Available') : 'Houseful';

    // Fetch footer data using the fetchFooterData helper function
    $footer_data = $this->fetchFooterData();

    // Add footer data to the data array
    $data['latest_footer_blogs'] = $footer_data['latest_footer_blogs']; // Footer latest blogs
    $data['categories_with_post_count'] = $footer_data['categories_with_post_count']; // Footer category post counts

    // Load the event detail view
    return view('events/detail', $data);
}

    
}
