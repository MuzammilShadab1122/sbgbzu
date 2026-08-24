<?php
// includes/data.php

$highlights = [
    'monthLabel' => 'April 2026',
    'starOfMonthId' => 5,
    'monthlyGrinders' => [23, 8]
];

$events = [
    [
        'id' => 201,
        'title' => 'Explore AWS with AWS Student Builder Group BZU (formerly AWS Cloud Club)',
        'type' => 'past',
        'date' => 'Mar 29, 2026',
        'location' => 'Online (Google Meet)',
        'description' => 'Introduction to the chapter, program structure, points system, and how teams will operate.',
        'gallery' => [
            'public/images/firstevent/1.jpeg',
            'public/images/firstevent/2.jpeg',
            'public/images/firstevent/3.jpeg',
            'public/images/firstevent/4.jpeg',
            'public/images/firstevent/5.jpeg',
        ]
    ],
    [
        'id' => 202,
        'title' => 'Onboarding Session (Physical), Team Induction',
        'type' => 'past',
        'date' => 'May 07, 2026',
        'location' => 'BZU Campus',
        'description' => 'Physical onboarding: member introductions, team allocation, roadmap, and first community tasks.',
        'gallery' => [
            'public/images/onboarding/1.jpeg',
            'public/images/onboarding/2.jpeg',
            'public/images/onboarding/3.jpeg',
            'public/images/onboarding/4.jpeg',
            'public/images/onboarding/5.jpeg',
            'public/images/onboarding/6.jpeg',
            'public/images/onboarding/7.jpeg',
            'public/images/onboarding/8.jpeg',
            'public/images/onboarding/9.jpeg',
            'public/images/onboarding/10.jpeg',
            'public/images/onboarding/11.jpeg',
            'public/images/onboarding/12.jpeg',
            'public/images/onboarding/13.jpeg',
        ]
    ],
    [
        'id' => 101,
        'title' => 'AWS Tech Fusion',
        'type' => 'upcoming',
        'date' => 'May 12, 2026',
        'location' => 'First Floor, Arcade Plaza, Sector C, DHA',
        'description' => 'Join us for an exciting community meetup packed with insights, opportunities, and networking!. Whether you’re a beginner or a cloud enthusiast, this meetup is the perfect place to learn, connect, and grow together in the cloud journey.',
        'image' => 'public/images/techfusion.jpeg', 
    ]
];

$posts = [
    [
        'id' => 1,
        'title' => 'Welcome to AWS Student Builder Group — BZU',
        'category' => 'Announcement',
        'date' => 'Apr 18, 2026',
        'excerpt' => 'We’re building a student-driven cloud community focused on learning, collaboration, and shipping real AWS projects.'
    ],
    [
        'id' => 2,
        'title' => 'How points work in our leaderboard',
        'category' => 'Program',
        'date' => 'Apr 21, 2026',
        'excerpt' => 'Points reward learning, participation, projects, mentorship, and consistency. This post explains the rules clearly.'
    ],
    [
        'id' => 3,
        'title' => 'Online orientation recap + next steps',
        'category' => 'Event',
        'date' => 'Apr 20, 2026',
        'excerpt' => 'Quick recap of the online orientation and the next steps for each team.'
    ],
    [
        'id' => 4,
        'title' => 'Onboarding session highlights (Physical)',
        'category' => 'Event',
        'date' => 'Apr 27, 2026',
        'excerpt' => 'Team induction, roadmap, and how we’ll execute events, technical tasks, and content.'
    ],
    [
        'id' => 5,
        'title' => 'Upcoming: May 12 AWS workshop — what to prepare',
        'category' => 'Announcement',
        'date' => 'May 09, 2026',
        'excerpt' => 'Bring your laptop, AWS account (if possible), and be ready for hands-on practice. Details inside.'
    ]
];

$participants = [
    [
        'id' => 1,
        'name' => 'Muhammad Mehdi Hassan',
        'role' => 'Leader',
        'team' => 'Core',
        'level' => 'Lead',
        'points' => 0.0,
        'campus' => 'BZU',
        'responsibilities' => 'Overall leadership and strategic direction of the AWS Student Club at BZU.',
        'image' => 'public/images/AWS-MembersPics/Mehdi Hassan.jpeg',
    ],
    [
        'id' => 2,
        'name' => 'Mirza Zaryab',
        'role' => 'Vice Leader',
        'team' => 'Core',
        'level' => 'Core',
        'points' => 52.13636364,
        'campus' => 'BZU',
        'responsibilities' => 'Assisting the leader in managing club activities, coordinating between teams, and ensuring smooth operations.',
        'image' => 'public/images/AWS-MembersPics/Zaryab.jpeg',
    ],
    [
        'id' => 3,
        'name' => 'Hamid Ali',
        'role' => 'Executive Leader',
        'team' => 'Core',
        'level' => 'Core',
        'points' => 66.200,
        'campus' => 'MNS-UET',
        'responsibilities' => 'Overseeing daily operations and ensuring the club runs smoothly.',
        'image' => 'public/images/AWS-MembersPics/Hamid Ali.jpg',
    ],
    [
        'id' => 4,
        'name' => 'Ali Sachal',
        'role' => 'Community Manager',
        'team' => 'Core',
        'level' => 'Core',
        'points' => 59.59090909,
        'campus' => 'BZU',
        'responsibilities' => 'Managing and growing the club\'s community engagement.',
        'image' => 'public/images/AWS-MembersPics/Ali Sachal.png',
    ],
    [
        'id' => 5,
        'name' => 'Ali Hassan',
        'role' => 'Directorate of Media, Design & Marketing',
        'team' => 'Core',
        'level' => 'Core',
        'points' => 64.54545455,
        'campus' => 'BZU',
        'responsibilities' => 'Leading the Media, Design & Marketing team to create engaging content and promote the club\'s activities.',
        'image' => 'public/images/AWS-MembersPics/Ali-Hassan.jpeg',
    ],
    [
        'id' => 6,
        'name' => 'Hammad Ahmad',
        'role' => 'Directorate of Events & Operations',
        'team' => 'Core',
        'level' => 'Core',
        'points' => 58.68181818,
        'campus' => 'BZU',
        'responsibilities' => 'Overseeing event planning and execution, as well as managing day-to-day operations.',
        'image' => 'public/images/AWS-MembersPics/Hammad Ahmad.jpg',
    ],
    [
        'id' => 7,
        'name' => 'Bushra Kanooz Khan',
        'role' => 'Associate Director Design',
        'team' => 'Media & Design',
        'level' => 'Core',
        'points' => 56.68181818,
        'campus' => 'BZU',
        'responsibilities' => 'Assisting in leading the Media & Design team, focusing on creating visual content and designs for the club.',
        'image' => 'public/images/AWS-MembersPics/Bushra.jpeg',
    ],
    [
        'id' => 8,
        'name' => 'Muhammad Bin Iftikhar',
        'role' => 'Member',
        'team' => 'Technical',
        'level' => 'Builder',
        'points' => 54.23636364,
        'campus' => 'BZU',
        'responsibilities' => 'Contributing to various tasks across teams as needed.',
        'image' => 'public/images/AWS-MembersPics/Muhammad.png',
    ],
    [
        'id' => 9,
        'name' => 'Aqib Hussain',
        'role' => 'DevOps Leader',
        'team' => 'Technical',
        'level' => 'Developer',
        'points' => 59.14545455,
        'campus' => 'BZU',
        'responsibilities' => 'Cloud Development & DevOps',
        'image' => 'public/images/AWS-MembersPics/Aqib.jpg',
    ],
    [
        'id' => 10,
        'name' => 'Ehsan Ur Rehman',
        'role' => 'Member',
        'team' => 'Media & Design',
        'level' => 'Builder',
        'points' => 47.72727273,
        'campus' => 'BZU',
        'responsibilities' => 'Marketing and Promotions',
        'image' => 'public/images/AWS-MembersPics/Muhammad Ehsan Ur Rehman.jpeg',
    ],
    [
        'id' => 11,
        'name' => 'Aleena Khan',
        'role' => 'Frontend Lead',
        'team' => 'Technical',
        'level' => 'Developer',
        'points' => 47.63636364,
        'campus' => 'BZU',
        'responsibilities' => 'Frontend Development',
        'image' => 'public/images/AWS-MembersPics/Aleena.jpg',
    ],
    [
        'id' => 12,
        'name' => 'Noor Ul Ain Fatima',
        'role' => 'Operations Manager',
        'team' => 'Operations',
        'level' => 'Core',
        'points' => 59.04545455,
        'campus' => 'BZU',
        'responsibilities' => 'Overseeing daily operations and ensuring the club runs smoothly.',
        'image' => 'public/images/AWS-MembersPics/Noor.jpg',
    ],
    [
        'id' => 13,
        'name' => 'Affan Ali',
        'role' => 'Assistant Event Director (M)',
        'team' => 'Events',
        'level' => 'Builder',
        'points' => 58.500,
        'campus' => 'BZU',
        'responsibilities' => 'Event Coordination',
        'image' => 'public/images/AWS-MembersPics/Affan.jpg',
    ],
    [
        'id' => 14,
        'name' => 'Hoor Fatima',
        'role' => 'Assistant Event Director (F)',
        'team' => 'Events',
        'level' => 'Builder',
        'points' => 56.04545455,
        'campus' => 'BZU',
        'responsibilities' => 'Event Coordination',
        'image' => 'public/images/AWS-MembersPics/Hoor.png',
    ],
    [
        'id' => 15,
        'name' => 'Abdul Saboor',
        'role' => 'Senior Event Coordinator',
        'team' => 'Events',
        'level' => 'Builder',
        'points' => 59.59090909,
        'campus' => 'BZU',
        'responsibilities' => 'Event Management',
        'image' => 'public/images/AWS-MembersPics/Abdul Saboor.jpeg',
    ],
    [
        'id' => 16,
        'name' => 'Hania Amjad',
        'role' => 'Senior Media Coordinator',
        'team' => 'Media & Design',
        'level' => 'Builder',
        'points' => 59.000,
        'campus' => 'BZU',
        'responsibilities' => 'Coordinating Media & Content creation for the club.',
        'image' => 'public/images/AWS-MembersPics/Hania Amjad.png',
    ],
    [
        'id' => 17,
        'name' => 'Moazam Ali',
        'role' => 'Assistant Marketing Manager',
        'team' => 'Marketing',
        'level' => 'Builder',
        'points' => 58.72727273,
        'campus' => 'BZU',
        'responsibilities' => 'Marketing and Promotions',
        'image' => 'public/images/AWS-MembersPics/Moazam Ali.png',
    ],
    [
        'id' => 18,
        'name' => 'Anbreen Akhtar',
        'role' => 'Member',
        'team' => 'Operations',
        'level' => 'Builder',
        'points' => 0.000,
        'campus' => 'BZU',
        'responsibilities' => 'Operations Support and Coordination',
        'image' => 'public/images/AWS-MembersPics/Ambreen.jpeg',
    ],
    [
        'id' => 19,
        'name' => 'Muhammad Rizwan',
        'role' => 'Backend Developer(Member)',
        'team' => 'Technical',
        'level' => 'Builder',
        'points' => 0.000,
        'campus' => 'BZU',
        'responsibilities' => 'Backend Development',
        'image' => 'public/images/AWS-MembersPics/Rizwan.jpeg',
    ],
    [
        'id' => 20,
        'name' => 'Mujahid Javaid',
        'role' => 'Member',
        'team' => 'Marketing',
        'level' => 'Builder',
        'points' => 0.000,
        'campus' => 'BZU',
        'responsibilities' => 'Community Growth & marketing',
        'image' => 'public/images/AWS-MembersPics/Mujahid.jpeg',
    ], 
    [
        'id' => 21,
        'name' => 'Uzair',
        'role' => 'Member',
        'team' => 'Marketing',
        'level' => 'Builder',
        'points' => 0.000,
        'campus' => 'BZU',
        'responsibilities' => 'Outreach and Promotions',
        'image' => 'public/images/AWS-MembersPics/Uzair.jpeg',
    ],
    [
        'id' => 22,
        'name' => 'Saim Bin Zahid',
        'role' => 'Member',
        'team' => 'Media & Design',
        'level' => 'Builder',
        'points' => 0.000,
        'campus' => 'BZU',
        'responsibilities' => 'Content Creation',
        'image' => 'public/images/AWS-MembersPics/Saim.jpeg',
    ],
    [
        'id' => 23,
        'name' => 'Muhammad Zohaib',
        'role' => 'Member',
        'team' => 'Marketing',
        'level' => 'Builder',
        'points' => 0.000,
        'campus' => 'BZU',
        'responsibilities' => 'Promotions',
        'image' => 'public/images/AWS-MembersPics/Zohaib.jpeg',
    ],
    [
        'id' => 24,
        'name' => 'Muzammil Shahdab',
        'role' => 'AI Engineer(Member)',
        'team' => 'Technical',
        'level' => 'Builder',
        'points' => 0.000,
        'campus' => 'BZU',
        'responsibilities' => 'AI Engineering and Research',
        'image' => 'public/images/AWS-MembersPics/Muzammil Shahdab.jpg',
    ],
];

// Helper functions for teams and sorting
$team_meta = [
    'Core' => ['title' => 'Core Team', 'blurb' => 'Chapter leadership and core coordinators.'],
    'Technical' => ['title' => 'Technical Team', 'blurb' => 'Cloud development, DevOps, and AI engineering builders.'],
    'Media & Design' => ['title' => 'Media & Design Team', 'blurb' => 'Graphic design, UI/UX design, and videography creators.'],
    'Marketing' => ['title' => 'Marketing Team', 'blurb' => 'Social media marketing, promotion, and public relations.'],
    'Events' => ['title' => 'Events Team', 'blurb' => 'Event planning, orchestration, and operational execution.'],
    'Operations' => ['title' => 'Operations Team', 'blurb' => 'Daily operations management, documentation, and coordination.'],
];

$team_order = ['Core', 'Technical', 'Media & Design', 'Marketing', 'Events', 'Operations'];

if (!function_exists('get_team_members')) {
    function get_team_members($team_name, $members) {
        $filtered = [];
        foreach ($members as $m) {
            if ($m['team'] === $team_name) {
                $filtered[] = $m;
            }
        }
        usort($filtered, function($a, $b) {
            return $b['points'] <=> $a['points'];
        });
        return $filtered;
    }
}
