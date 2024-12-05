<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;
use Validator;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $default_shortcodes = [
            [
                'title' => ___('Site Title'),
                'code' => '{SITE_TITLE}'
            ],
            [
                'title' => ___('Site URL'),
                'code' => '{SITE_URL}'
            ]
        ];
        $email_template = [
            [
                'id' => 'new-order',
                'title' => ___('New Order Email to Restaurant Owners'),
                'subject' => 'email_sub_new_order',
                'message' => 'email_message_new_order',
                'shortcodes' => array_merge($default_shortcodes, [
                    [
                        'title' => ___('Restaurant Name'),
                        'code' => '{RESTAURANT_NAME}'
                    ],
                    [
                        'title' => ___('Order Type'),
                        'code' => '{ORDER_TYPE}'
                    ],
                    [
                        'title' => ___('Customer Name'),
                        'code' => '{CUSTOMER_NAME}'
                    ],
                    [
                        'title' => ___('Table Number'),
                        'code' => '{TABLE_NUMBER}'
                    ],
                    [
                        'title' => ___('Phone Number'),
                        'code' => '{PHONE_NUMBER}'
                    ],
                    [
                        'title' => ___('Address'),
                        'code' => '{ADDRESS}'
                    ],
                    [
                        'title' => ___('Order Details'),
                        'code' => '{ORDER}'
                    ],
                    [
                        'title' => ___('Customer Message'),
                        'code' => '{MESSAGE}'
                    ],
                ]),
            ],
            [
                'id' => 'signup-details',
                'title' => ___('New User Account Details Email'),
                'subject' => 'email_sub_signup_details',
                'message' => 'email_message_signup_details',
                'shortcodes' => array_merge($default_shortcodes, [
                    [
                        'title' => ___('User ID'),
                        'code' => '{USER_ID}'
                    ],
                    [
                        'title' => ___('Username'),
                        'code' => '{USERNAME}'
                    ],
                    [
                        'title' => ___('User Full Name'),
                        'code' => '{USER_FULLNAME}'
                    ],
                    [
                        'title' => ___('User Email'),
                        'code' => '{EMAIL}'
                    ]
                ]),
            ],
            [
                'id' => 'create-account',
                'title' => ___('New User Confirmation Email'),
                'subject' => 'email_sub_signup_confirm',
                'message' => 'email_message_signup_confirm',
                'shortcodes' => array_merge($default_shortcodes, [
                    [
                        'title' => ___('User ID'),
                        'code' => '{USER_ID}'
                    ],
                    [
                        'title' => ___('Username'),
                        'code' => '{USERNAME}'
                    ],
                    [
                        'title' => ___('User Full Name'),
                        'code' => '{USER_FULLNAME}'
                    ],
                    [
                        'title' => ___('User Email'),
                        'code' => '{EMAIL}'
                    ],
                    [
                        'title' => ___('Confirmation Link'),
                        'code' => '{CONFIRMATION_LINK}'
                    ]
                ]),
            ],
            [
                'id' => 'forgot-pass',
                'title' => ___('Forgot Password Email'),
                'subject' => 'email_sub_forgot_pass',
                'message' => 'email_message_forgot_pass',
                'shortcodes' => array_merge($default_shortcodes, [
                    [
                        'title' => ___('User ID'),
                        'code' => '{USER_ID}'
                    ],
                    [
                        'title' => ___('Username'),
                        'code' => '{USERNAME}'
                    ],
                    [
                        'title' => ___('User Full Name'),
                        'code' => '{USER_FULLNAME}'
                    ],
                    [
                        'title' => ___('User Email'),
                        'code' => '{EMAIL}'
                    ],
                    [
                        'title' => ___('Password Reset Link'),
                        'code' => '{FORGET_PASSWORD_LINK}'
                    ],
                    [
                        'title' => ___('Link Expire Time in Minutes'),
                        'code' => '{EXPIRY_TIME}'
                    ]
                ]),
            ],
            [
                'id' => 'contact_us',
                'title' => ___('Contact Us Email'),
                'subject' => 'email_sub_contact',
                'message' => 'email_message_contact',
                'shortcodes' => array_merge($default_shortcodes, [
                    [
                        'title' => ___('Sender Full Name'),
                        'code' => '{NAME}'
                    ],
                    [
                        'title' => ___('Sender Email'),
                        'code' => '{EMAIL}'
                    ],
                    [
                        'title' => ___('Contact Subject'),
                        'code' => '{CONTACT_SUBJECT}'
                    ],
                    [
                        'title' => ___('Contact Message'),
                        'code' => '{MESSAGE}'
                    ]
                ]),
            ],
            [
                'id' => 'feedback',
                'title' => ___('Feedback Email'),
                'subject' => 'email_sub_feedback',
                'message' => 'email_message_feedback',
                'shortcodes' => array_merge($default_shortcodes, [
                    [
                        'title' => ___('Sender Full Name'),
                        'code' => '{NAME}'
                    ],
                    [
                        'title' => ___('Sender Email'),
                        'code' => '{EMAIL}'
                    ],
                    [
                        'title' => ___('Sender Phone Number'),
                        'code' => '{PHONE}'
                    ],
                    [
                        'title' => ___('Feedback Subject'),
                        'code' => '{FEEDBACK_SUBJECT}'
                    ],
                    [
                        'title' => ___('Feedback Message'),
                        'code' => '{MESSAGE}'
                    ]
                ]),
            ],
        ];
        return view('admin.mailtemplates.index',
            compact('default_shortcodes', 'email_template')
        );

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $requestData = $request->except('email_setting', '_token');
        foreach ($requestData as $key => $value) {
            Option::updateOptions($key, $value);
        }

        $result = array('success' => true, 'message' => ___('Updated Successfully'));
        return response()->json($result, 200);
    }
}
